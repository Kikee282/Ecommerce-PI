<?php
// src/api_productes.php

// Indiquem que el que tornem és JSON
header('Content-Type: application/json');

// Connectem internament amb el contenidor de Docker (http://jsonserver:3000)
// Això funciona perquè és comunicació servidor a servidor, no passa pel navegador
$json = file_get_contents('http://jsonserver:3000/productes');

if ($json === false) {
    http_response_code(500);
    echo json_encode(["error" => "No s'ha pogut connectar amb el JSON Server"]);
} else {
    echo $json;
}
?>