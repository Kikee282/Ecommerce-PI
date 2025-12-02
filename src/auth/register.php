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

    // URL base de l'API (interna de Docker)
    $baseUrl = "http://jsonserver:3000/usuaris";

    // 2. COMPROVAR DUPLICATS (Nom d'usuari)
    $checkUserUrl = $baseUrl . "?nom_usuari=" . urlencode($nomUsuari);
    $existingUser = json_decode(file_get_contents($checkUserUrl), true);

    if (!empty($existingUser)) {
        die("Error: Aquest nom d'usuari ja existeix. <a href='registerForm.html'>Torna-ho a provar</a>");
    }

    // 3. COMPROVAR DUPLICATS (Email) - NOVA VALIDACIÓ
    $checkEmailUrl = $baseUrl . "?email=" . urlencode($email);
    $existingEmail = json_decode(file_get_contents($checkEmailUrl), true);

    if (!empty($existingEmail)) {
        die("Error: Aquest correu electrònic ja està registrat. <a href='login.html'>Inicia sessió</a>");
    }

    // 4. Xifrar contrasenya
    $password_hash = password_hash($raw_pass, PASSWORD_DEFAULT);

    // 5. Preparar dades per a l'API
    // Per defecte, el rol serà 'user'. Només es pot fer admin tocant la BD manualment.
    $newUser = [
        "nom_usuari" => $nomUsuari,
        "contrasenya" => $password_hash,
        "email" => $email,
        "nom" => $nom,
        "cognoms" => $cognoms,
        "role" => "user", 
        "data_registre" => date('c')
    ];

    // 6. Enviar a JSON Server (POST)
    // Usem stream_context en lloc de cURL per consistència i simplicitat
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($newUser),
            'ignore_errors' => true
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents($baseUrl, false, $context);
    
    // Obtenim el codi de resposta
    $httpCode = 0;
    if (isset($http_response_header[0])) {
        preg_match('#HTTP/\S+\s+(\d{3})#', $http_response_header[0], $matches);
        $httpCode = (int)$matches[1];
    }

    if ($httpCode === 201) { // 201 Created
        // Redirigir al login si tot ha anat bé
        header('Location: login.html');
        exit;
    } else {
        echo "Error al registrar l'usuari a l'API.";
    }
}
?>