<?php
// 1. Carregar l'autoloader de Composer
require 'vendor/autoload.php';

// Importar les classes necessàries
use PhpOffice\PhpSpreadsheet\IOFactory;

// --- DEFINICIÓ DE RUTES ---

// Ruta on desarem temporalment l'Excel pujat
$uploadsDir = '/var/www/html/uploads/'; 

// Ruta del fitxer MESTRE de la base de dades (JSON Server)
$jsonDatabaseFile = '/data/db.json';


// --- 2. REBRE I DESAR EL FITXER ---

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    
    $fileName = basename($_FILES['excel_file']['name']);
    $targetFilePath = $uploadsDir . $fileName;
    
    // Movem el fitxer pujat a la carpeta 'uploads'
    if (move_uploaded_file($_FILES['excel_file']['tmp_name'], $targetFilePath)) {
        
        echo "Fitxer Excel rebut correctament.<br>";
        
        // --- 3. LLEGIR EL CONTINGUT DE L'EXCEL ---
        
        try {
            $spreadsheet = IOFactory::load($targetFilePath);
            $sheet = $spreadsheet->getActiveSheet();
            // Convertim les dades a array (sense fórmules, formatat, ignorant buits)
            $data = $sheet->toArray(null, true, true, true);

            // Eliminem el fitxer temporal (Excel)
            unlink($targetFilePath);

            // --- 4. PROCESSAR DADES ---
            
            $productes = [];
            $errors = [];
            $importedCount = 0;
            $idCounter = 1; 

            // Iterem per cada fila (ignorem la fila 1 = capçaleres)
            foreach (array_slice($data, 1) as $rowIndex => $row) {
                
                // Ús de '?? ""' per evitar errors "Deprecated: trim(null)"
                $sku = trim($row['A'] ?? '');
                $nom = trim($row['B'] ?? '');
                $descripcio = trim($row['C'] ?? '');
                $img = trim($row['D'] ?? '');
                $preu = $row['E']; 
                $estoc = $row['F'];

                // Validació simple
                if (!empty($nom) && !empty($sku) && is_numeric($preu) && is_numeric($estoc)) {
                    $productes[] = [
                        'id' => $idCounter++,
                        'sku' => $sku,
                        'nom' => $nom,
                        'descripcio' => $descripcio,
                        'img' => $img,
                        'preu' => (float)$preu,
                        'estoc' => (int)$estoc
                    ];
                    $importedCount++;
                } else {
                    // Si la fila està buida o malament, l'ignorem i guardem l'error
                    if (!empty($sku) || !empty($nom)) { 
                        $errors[] = "Fila " . ($rowIndex + 2) . " ignorada: Dades invàlides.";
                    }
                }
            }

            // --- 5. ACTUALITZAR EL FITXER db.json ---

            // A. Llegim les dades actuals (per no perdre els usuaris)
            $currentData = [];
            if (file_exists($jsonDatabaseFile)) {
                $jsonContent = file_get_contents($jsonDatabaseFile);
                $currentData = json_decode($jsonContent, true) ?? [];
            }

            // B. Actualitzem la llista de PRODUCTES (reemplacem l'antiga)
            $currentData['productes'] = $productes;

            // C. Assegurem que la llista d'USUARIS es manté (o es crea si no existeix)
            if (!isset($currentData['usuaris'])) {
                $currentData['usuaris'] = [];
            }

            // D. Guardem tot de nou al fitxer
            if (file_put_contents($jsonDatabaseFile, json_encode($currentData, JSON_PRETTY_PRINT))) {
                
                // --- 6. RESULTATS ---
                echo "<h3 style='color:green;'>Importació completada!</h3>";
                echo "Total de productes importats: <strong>$importedCount</strong>.<br>";
                
                if (!empty($errors)) {
                    echo "<h4 style='color:orange;'>Avisos:</h4><ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo "</ul>";
                }
                
                echo "<p>El JSON Server s'ha actualitzat. Comprova-ho a: ";
                echo "<a href='http://localhost:3000/productes' target='_blank'>http://localhost:3000/productes</a></p>";
                
            } else {
                echo "<h3 style='color:red;'>ERROR: No s'ha pogut escriure a '$jsonDatabaseFile'.</h3>";
            }

        } catch (Exception $e) {
            echo "<h3 style='color:red;'>Error llegint l'Excel:</h3>" . $e->getMessage();
        }
        
    } else {
        echo "<h3 style='color:red;'>Error pujant el fitxer (Comprova permisos de la carpeta uploads).</h3>";
    }
} else {
    echo "Error: Accés no vàlid.";
}

echo "<br><br><a href='admin_import.html'>Tornar al formulari</a>";
?>