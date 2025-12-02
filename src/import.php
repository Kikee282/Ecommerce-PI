<?php
/**
 * Script d'importació de productes des d'un fitxer Excel (.xlsx)
 * * Aquest script s'encarrega de:
 * 1. Rebre un fitxer pujat mitjançant un formulari HTML.
 * 2. Processar el fitxer Excel utilitzant la llibreria PhpSpreadsheet.
 * 3. Validar i extreure les dades de cada producte.
 * 4. Actualitzar la base de dades JSON (db.json) amb els nous productes.
 * 5. Informar de l'estat de la importació.
 */

// 1. Càrrega de dependències
// Utilitzem l'autoloader de Composer per carregar automàticament les classes necessàries,
// com ara PhpSpreadsheet, sense haver de fer 'require' manualment per a cadascuna.
require 'vendor/autoload.php';

// Importem la classe IOFactory de PhpSpreadsheet, que ens permetrà llegir diferents formats de fulls de càlcul.
use PhpOffice\PhpSpreadsheet\IOFactory;

// --- DEFINICIÓ DE RUTES I CONSTANTS ---

// Directori temporal on es guardarà el fitxer Excel pujat abans de ser processat.
// Nota: Aquest directori ha de tenir permisos d'escriptura (chmod 777 en entorns de desenvolupament).
$uploadsDir = '/var/www/html/uploads/'; 

// Ruta absoluta al fitxer que actua com a base de dades per al JSON Server.
// Aquest fitxer conté tant els productes com els usuaris i comentaris.
$jsonDatabaseFile = '/data/db.json';


// --- 2. REBRE I DESAR EL FITXER ---

// Comprovem que la petició sigui de tipus POST i que s'hagi enviat un fitxer amb el nom 'excel_file'.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    
    // Obtenim el nom original del fitxer pujat.
    $fileName = basename($_FILES['excel_file']['name']);
    // Construïm la ruta completa on es desarà el fitxer.
    $targetFilePath = $uploadsDir . $fileName;
    
    // Movem el fitxer des del directori temporal del sistema (tmp_name) al nostre directori de destí.
    if (move_uploaded_file($_FILES['excel_file']['tmp_name'], $targetFilePath)) {
        
        echo "Fitxer Excel rebut correctament.<br>";
        
        // --- 3. LLEGIR EL CONTINGUT DE L'EXCEL ---
        
        try {
            // Carreguem el fitxer Excel utilitzant la fàbrica IOFactory.
            // Aquesta funció detecta automàticament el format del fitxer (Xlsx, Xls, Csv, etc.).
            $spreadsheet = IOFactory::load($targetFilePath);
            
            // Obtenim el full actiu (el primer full del document).
            $sheet = $spreadsheet->getActiveSheet();
            
            // Convertim totes les dades del full a un array multidimensional de PHP.
            // Paràmetres: (null = tot el full, true = calcular fórmules, true = formatar dades, true = indexar per fila/columna).
            $data = $sheet->toArray(null, true, true, true);

            // Una vegada tenim les dades en memòria, ja no necessitem el fitxer físic.
            // L'eliminem per no ocupar espai al servidor.
            unlink($targetFilePath);

            // --- 4. PROCESSAR I VALIDAR DADES ---
            
            $productes = [];     // Array on guardarem els productes vàlids.
            $errors = [];        // Array per registrar errors de files no vàlides.
            $importedCount = 0;  // Comptador de productes importats amb èxit.
            $idCounter = 1;      // Comptador per generar IDs seqüencials per als productes.

            // Iterem per cada fila de l'Excel.
            // Utilitzem array_slice per ignorar la primera fila (índex 0), ja que conté les capçaleres.
            foreach (array_slice($data, 1) as $rowIndex => $row) {
                
                // Extracció de dades de cada columna (A, B, C...).
                // Utilitzem l'operador de coalescència nul·la (?? '') per evitar errors si la cel·la està buida
                // i trim() per netejar espais en blanc al principi i al final.
                
                $sku = trim($row['A'] ?? '');        // Columna A: SKU (Referència)
                $nom = trim($row['B'] ?? '');        // Columna B: Nom del producte
                $descripcio = trim($row['C'] ?? ''); // Columna C: Descripció
                $img = trim($row['D'] ?? '');        // Columna D: Ruta de la imatge
                $preu = $row['E'];                   // Columna E: Preu (no fem trim encara per validar si és numèric)
                $estoc = $row['F'];                  // Columna F: Estoc

                // Validació simple:
                // 1. El nom i el SKU són obligatoris.
                // 2. El preu i l'estoc han de ser valors numèrics.
                if (!empty($nom) && !empty($sku) && is_numeric($preu) && is_numeric($estoc)) {
                    
                    // Si la fila és vàlida, creem l'objecte producte i l'afegim a la llista.
                    $productes[] = [
                        'id' => $idCounter++,          // Assignem un nou ID seqüencial
                        'sku' => $sku,
                        'nom' => $nom,
                        'descripcio' => $descripcio,
                        'img' => $img,
                        'preu' => (float)$preu,        // Convertim a flotant per a decimals
                        'estoc' => (int)$estoc         // Convertim a enter
                    ];
                    $importedCount++;
                } else {
                    // Si la fila no compleix la validació, registrem l'error ignorant files totalment buides.
                    if (!empty($sku) || !empty($nom)) { 
                        // Sumem 2 al rowIndex perquè l'array comença a 0 i hem saltat la capçalera.
                        $errors[] = "Fila " . ($rowIndex + 2) . " ignorada: Dades invàlides (Falten camps o format incorrecte).";
                    }
                }
            }

            // --- 5. ACTUALITZAR EL FITXER db.json ---
            // L'objectiu és actualitzar només la llista de productes sense esborrar els usuaris o altres dades.

            // A. Llegim les dades actuals del fitxer JSON.
            $currentData = [];
            if (file_exists($jsonDatabaseFile)) {
                $jsonContent = file_get_contents($jsonDatabaseFile);
                $currentData = json_decode($jsonContent, true) ?? [];
            }

            // B. Actualitzem la clau 'productes' amb la nova llista que acabem de generar.
            // Això REEMPLAÇA tot el catàleg anterior amb el del nou Excel.
            $currentData['productes'] = $productes;

            // C. Assegurem que la clau 'usuaris' existeix per no trencar la integritat del fitxer.
            if (!isset($currentData['usuaris'])) {
                $currentData['usuaris'] = [];
            }
            
            // També podríem assegurar que existeixen 'comentaris' o 'likes' si fos necessari.
            // if (!isset($currentData['comentaris'])) $currentData['comentaris'] = [];

            // D. Guardem tot l'array (productes nous + usuaris vells) al fitxer db.json.
            // JSON_PRETTY_PRINT fa que el fitxer sigui llegible per humans (amb salts de línia i espais).
            if (file_put_contents($jsonDatabaseFile, json_encode($currentData, JSON_PRETTY_PRINT))) {
                
                // --- 6. RESULTATS I FEEDBACK ---
                
                echo "<h3 style='color:green;'>Importació completada amb èxit!</h3>";
                echo "Total de productes importats: <strong>$importedCount</strong>.<br>";
                
                // Si hi ha hagut errors en alguna fila, els mostrem en una llista.
                if (!empty($errors)) {
                    echo "<h4 style='color:orange;'>Avisos (Files no importades):</h4><ul>";
                    foreach ($errors as $error) {
                        echo "<li>$error</li>";
                    }
                    echo "</ul>";
                }
                
                // Enllaç per verificar visualment l'API.
                echo "<p>El JSON Server s'ha actualitzat. Pots comprovar-ho a: ";
                echo "<a href='http://localhost:3000/productes' target='_blank'>http://localhost:3000/productes</a></p>";
                
            } else {
                echo "<h3 style='color:red;'>ERROR CRÍTIC: No s'ha pogut escriure al fitxer '$jsonDatabaseFile'.</h3>";
                echo "<p>Verifica els permisos d'escriptura de la carpeta /data/.</p>";
            }

        } catch (Exception $e) {
            // Captura d'errors específics de la lectura de l'Excel (format invàlid, fitxer corrupte, etc.)
            echo "<h3 style='color:red;'>Error llegint el fitxer Excel:</h3>" . $e->getMessage();
        }
        
    } else {
        echo "<h3 style='color:red;'>Error pujant el fitxer al servidor.</h3>";
        echo "<p>Comprova que la carpeta 'uploads' existeix i té permisos d'escriptura.</p>";
    }
} else {
    echo "Error: Accés no vàlid. Has d'utilitzar el formulari.";
}

// Botó per tornar al formulari d'importació
echo "<br><br><a href='admin_import.html'>Tornar al formulari</a>";
?>