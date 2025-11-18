<?php
// 1. Iniciem la sessió per poder accedir-hi i destruir-la
session_start();

// 2. Buidem totes les variables de sessió (per seguretat immediata)
$_SESSION = [];

// 3. REQUISIT: Eliminar la cookie d'identificació 'user_id'
// Posem la data de caducitat en el passat (time() - 3600) perquè el navegador l'esborri
setcookie('user_id', '', time() - 3600, "/");

// (Opcional però recomanat) Esborrar també la cookie pròpia de la sessió PHP
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. REQUISIT: Destruir la sessió al servidor
session_destroy();

// 5. REQUISIT: Redirigir l'usuari a la pàgina d'inici
header("Location: ../index.php");
exit;
?>