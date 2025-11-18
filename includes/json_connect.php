<?php
/**
 * includes/json_connect.php
 * * Funcions d'ajuda per a interactuar amb el JSON Server via HTTP (cURL).
 */

/**
 * Defineix la URL base del servei JSON Server.
 * Aquest nom ('json-server') funciona perquè Docker l'afegeix
 * al seu DNS intern.
 */
define('JSON_SERVER_URL', 'http://json-server:3000/');

/**
 * Realitza una petició HTTP genèrica al JSON Server usant cURL.
 *
 * @param string $url L'URL completa de l'endpoint.
 * @param string $method El mètode HTTP (GET, POST, PATCH, DELETE).
 * @param array|null $data Les dades a enviar (per a POST o PATCH).
 * @return array|null La resposta decodificada de JSON Server o null si hi ha error.
 */
function callJsonServer($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HEADER => false,
    ];

    if ($method === 'POST' || $method === 'PATCH') {
        $jsonData = json_encode($data);
        $options[CURLOPT_POSTFIELDS] = $jsonData;
        $options[CURLOPT_HTTPHEADER] = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ];
    }

    curl_setopt_array($ch, $options);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_errno($ch);
    
    curl_close($ch);

    if ($curl_error) {
        // En una aplicació real, registrariem aquest error en un log.
        error_log("cURL Error ($url): " . curl_error($ch));
        return null;
    }
    
    // Comprovem codis d'èxit
    if ($http_code >= 200 && $http_code < 300) {
        return json_decode($response, true);
    } else {
        // En una aplicació real, registrariem aquest error en un log.
        error_log("HTTP Error ($url): " . $http_code);
        return null; // O la resposta d'error si preferim gestionar-la
    }
}

/**
 * Cerca un usuari per el seu nom d'usuari.
 *
 * @param string $nomUsuari
 * @return array|null L'usuari trobat o null si no existeix.
 */
function findUserByUsername($nomUsuari) {
    // Escapem el nom d'usuari per a la URL
    $nomUsuariEscapat = urlencode($nomUsuari);
    
    // JSON Server permet filtrar amb GET /usuaris?nom_usuari=...
    $url = JSON_SERVER_URL . 'usuaris?nom_usuari=' . $nomUsuariEscapat;
    
    $result = callJsonServer($url, 'GET');
    
    // Si la resposta és vàlida i no està buida, retornem el primer usuari
    if ($result !== null && !empty($result)) {
        return $result[0]; // Retorna el primer element de l'array
    }
    
    return null;
}

/**
 * Afegeix un nou usuari al JSON Server.
 *
 * @param array $userData Les dades del nou usuari.
 * @return array|null L'usuari creat o null si hi ha error.
 */
function createUser($userData) {
    $url = JSON_SERVER_URL . 'usuaris';
    return callJsonServer($url, 'POST', $userData);
}


// --- ⚠️ NOVES FUNCIONS NECESSÀRIES PER A PROFILE.PHP ---

/**
 * Cerca un usuari per el seu ID.
 * (Funció per a la línia 64 de profile.php)
 *
 * @param int|string $id L'ID de l'usuari.
 * @return array|null L'usuari trobat o null si no existeix.
 */
function findUserById($id) {
    // GET /usuaris/{id}
    $url = JSON_SERVER_URL . 'usuaris/' . $id;
    return callJsonServer($url, 'GET');
}

/**
 * Actualitza parcialment un usuari al JSON Server.
 * (Funció per a la línia 50 de profile.php)
 *
 * @param int|string $id L'ID de l'usuari a actualitzar.
 * @param array $data Les dades a actualitzar.
 * @return array|null L'usuari actualitzat o null si hi ha error.
 */
function updateUser($id, $data) {
    // PATCH /usuaris/{id}
    $url = JSON_SERVER_URL . 'usuaris/' . $id;
    return callJsonServer($url, 'PATCH', $data);
}

?>