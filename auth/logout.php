<?php
/**
 * auth/logout.php
 * * 🚪 Tanca la sessió i elimina la cookie.
 * S'encarrega de destruir la sessió de PHP i esborrar
 * la cookie d'autenticació del navegador de l'usuari.
 */

// 1. Iniciem la sessió
// Cal iniciar-la per a poder accedir-hi i destruir-la.
session_start();

// 2. 🧹 Destruïm totes les variables de la sessió
$_SESSION = [];

// 3. ❌ Eliminem la cookie del navegador
// Ho fem enviant una cookie amb el mateix nom ('user_id')
// però amb una data d'expiració en el passat (time() - 3600).
// El path "/" és important per a assegurar-nos que esborrem
// la cookie correcta.
if (isset($_COOKIE['user_id'])) {
    unset($_COOKIE['user_id']); // Elimina de PHP
    setcookie('user_id', '', time() - 3600, "/"); // Indica al navegador que l'esborre
}

// 4. Finalment, destruïm la sessió del servidor
session_destroy();

// 5. 🔁 Redirigim l'usuari a la pàgina d'inici de sessió.
// És important fer un 'exit' després d'una redirecció
// per a aturar l'execució de la resta de l'script.
header('Location: login.php');
exit;

?>