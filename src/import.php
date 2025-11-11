<?php
// 1. Carregar l'autoloader de Composer
// Això ens dóna accés a la llibreria PhpSpreadsheet
require 'vendor/autoload.php';

// Importar les classes necessàries
use PhpOffice\PhpSpreadsheet\IOFactory;

// --- DEFINICIÓ DE RUTES ---

// Ruta on desarem temporalment l'Excel pujat (dins del contenidor)
$uploadsDir = '/var/www/html/uploads/'; 

// Ruta ON ESCRIVIM el JSON FINAL (la que vigila JSON Server)
// Gràcies al volum de Docker, aquesta ruta és accessible per a PHP
$jsonOutputFile = '/data/products.json';


// --- 2. REBRE I DESAR EL FITXER ---

// Comprovem que la petició és POST i que el fitxer s'ha pujat
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    
    $fileName = basename($_FILES['excel_file']['name']);
    $targetFilePath = $uploadsDir . $fileName;
    
    // Movem el fitxer pujat a la nostra carpeta 'uploads'
    if (move_uploaded_file($_FILES['excel_file']['tmp_name'], $targetFilePath)) {
        
        echo "Fitxer Excel rebut correctament.<br>";
        
        // --- 3. LLEGIR EL CONTINGUT DE L'EXCEL ---
        
        try {
            // Carreguem el fitxer Excel
            $spreadsheet = IOFactory::load($targetFilePath);
            // Obtenim la primera fulla de càlcul
            $sheet = $spreadsheet->getActiveSheet();
            // Convertim les dades de la fulla a un array
            // true = Formata les dades, true = Calcula fórmules, true = Només files amb dades
            $data = $sheet->toArray(null, true, true, true);

            // Eliminem el fitxer temporal, ja no el necessitem
            unlink($targetFilePath);

            // --- 4 & 5. VALIDAR DADES I GENERAR L'ARRAY JSON ---
            
            $productes = [];
            $errors = [];
            $importedCount = 0;
            $idCounter = 1; // Per generar IDs únics

            // Iterem per cada fila (ignorem la fila 1, que són les capçaleres)
            foreach (array_slice($data, 1) as $rowIndex => $row) {
                // Suposem l'ordre de les columnes: A=SKU, B=Nom, C=Descripcio, D=Img, E=Preu, F=Estoc
                $sku = trim($row['A'] ?? '');
                $nom = trim($row['B'] ?? '');
                $descripcio = trim($row['C'] ?? '');
                $img = trim($row['D'] ?? '');
                $preu = $row['E']; // Aquests ja els valida 'is_numeric'
                $estoc = $row['F'];

                // Validació simple
                if (!empty($nom) && !empty($sku) && is_numeric($preu) && is_numeric($estoc)) {
                    $productes[] = [
                        'id' => $idCounter++,
                        'sku' => $sku,
                        'nom' => $nom,
                        'descripcio' => $descripcio,
                        'img' => $img,
                        'preu' => (float)$preu, // Assegurem que és un número
                        'estoc' => (int)$estoc  // Assegurem que és un enter
                    ];
                    $importedCount++;
                } else {
                    $errors[] = "Fila " . ($rowIndex + 2) . " ignorada per dades invàlides (SKU: $sku, Nom: $nom).";
                }
            }

            // Creem l'estructura final que espera JSON Server
            $jsonData = [
                'productes' => $productes
            ];

            // --- 6. DESAR L'ARXIU JSON ---
            
            // Convertim l'array a JSON bonic (JSON_PRETTY_PRINT)
            // i el desem directament a la carpeta /data/products.json
            if (file_put_contents($jsonOutputFile, json_encode($jsonData, JSON_PRETTY_PRINT))) {
                
                // --- 7. MOSTRAR RESULTAT FINAL ---
                echo "<h3 style='color:green;'>Importació completada!</h3>";
                echo "Total de productes importats: <strong>$importedCount</strong>.<br>";
                
                if (!empty($errors)) {
                    echo "<h4 style='color:orange;'>Fitxers amb errors (ignorats):</h4>";
                    echo "<ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo "</ul>";
                }
                
                echo "<p>El JSON Server s'ha actualitzat. Comprova-ho a: ";
                echo "<a href='http://localhost:3000/productes' target='_blank'>http://localhost:3000/productes</a></p>";
                
            } else {
                echo "<h3 style='color:red;'>ERROR: No s'ha pogut escriure el fitxer JSON a '$jsonOutputFile'. Comprova els permisos de la carpeta 'data'.</h3>";
            }

        } catch (Exception $e) {
            echo "<h3 style='color:red;'>Error llegint el fitxer Excel:</h3>";
            echo $e->getMessage();
        }
        
    } else {
        echo "<h3 style='color:red;'>Error pujant el fitxer.</h3>";
    }
} else {
    echo "Si us plau, puja un fitxer des del formulari.";
}

echo "<br><br><a href='admin_import.html'>Tornar al formulari</a>";

?>