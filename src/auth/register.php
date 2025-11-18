<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recollir dades
    $nomUsuari = trim($_POST['nom_usuari'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $raw_pass = $_POST['password'] ?? '';
    $nom = trim($_POST['nom'] ?? '');
    $cognoms = trim($_POST['cognoms'] ?? '');

    // Validació bàsica
    if (empty($nomUsuari) || empty($email) || empty($raw_pass)) {
        die("Error: Camps obligatoris buits.");
    }

    // 2. Comprovar duplicats (GET al JSON Server)
    // Nota: Dins de Docker, el host és 'jsonserver', no 'localhost'
    $apiUrl = "http://jsonserver:3000/usuaris";
    
    $checkUrl = $apiUrl . "?nom_usuari=" . urlencode($nomUsuari);
    
    // Iniciem cURL per comprovar usuari
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $checkUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $existingUsers = json_decode($response, true);

    if (!empty($existingUsers)) {
        die("Error: Aquest nom d'usuari ja existeix.");
    }

    // 3. Xifrar contrasenya
    $password_hash = password_hash($raw_pass, PASSWORD_DEFAULT);

    // 4. Preparar dades per a l'API
    $newUser = [
        "nom_usuari" => $nomUsuari,
        "contrasenya" => $password_hash, // Guardem el hash, no la plana
        "email" => $email,
        "nom" => $nom,
        "cognoms" => $cognoms,
        "data_registre" => date('c') // Format ISO 8601
    ];

    // 5. Enviar a JSON Server (POST)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($newUser));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 201) { // 201 Created
        // Redirigir al login si tot ha anat bé
        header('Location: login.html');
        exit;
    } else {
        echo "Error al registrar l'usuari a l'API.";
    }
}
?>