<?php
// Iniciem gestió de sessions PHP
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomUsuari = trim($_POST['nom_usuari'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nomUsuari) || empty($password)) {
        die("Error: Faltan dades.");
    }

    // 1. Buscar l'usuari a l'API (GET)
    // Filtrem per nom_usuari
    $apiUrl = "http://jsonserver:3000/usuaris?nom_usuari=" . urlencode($nomUsuari);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $users = json_decode($response, true);

    // Si l'array està buit, l'usuari no existeix
    if (empty($users)) {
        die("Error: Usuari o contrasenya incorrectes.");
    }

    // JSON Server torna un array, agafem el primer resultat
    $userFound = $users[0];

    // 2. Validar contrasenya (Hash vs Text pla)
    if (password_verify($password, $userFound['contrasenya'])) {
        
        // 3. Crear Sessió
        $_SESSION['user_id'] = $userFound['id'];
        $_SESSION['user_name'] = $userFound['nom_usuari'];
        $_SESSION['user_real_name'] = $userFound['nom'];

        // 4. Crear Cookie (segons requisits: 1 hora de durada)
        setcookie('user_id', $userFound['id'], time() + 3600, "/");

        echo "Login correcte! Benvingut " . htmlspecialchars($userFound['nom']);
        echo "<br><a href='../index.php'>Tornar a l'inici</a>";
        // Opcional: header('Location: index.html');
        
    } else {
        die("Error: Usuari o contrasenya incorrectes.");
    }
}
?>