<?php
/**
 * auth/login.php
 * * 🔑 Formulari i procés d’inici de sessió.
 * S'encarrega de verificar l'usuari i la contrasenya usant JSON Server
 * i crear la sessió i la cookie.
 */

// Incloem l'ajudant de connexió amb JSON Server
require_once '../includes/json_connect.php';

// Iniciem la sessió per a gestionar l'estat de login
// i per a passar missatges d'error.
session_start();

$error_message = '';

// Si l'usuari ja està logat (té una sessió activa),
// el redirigim directament al seu perfil.
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

// Comprovem si s'ha enviat el formulari (mètode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recollir dades del formulari
    $nom_usuari = trim($_POST['nom_usuari'] ?? '');
    $contrasenya = trim($_POST['contrasenya'] ?? '');

    // 2. Validació de dades
    if (empty($nom_usuari) || empty($contrasenya)) {
        $error_message = "Tots els camps són obligatoris.";
    } else {
        
        // 3. 🔎 Comprovar si l'usuari existeix
        $usuari = findUserByUsername($nom_usuari);

        if ($usuari !== null) {
            
            // 4. 🔐 Validar la contrasenya
            // Comprovem la contrasenya enviada amb el hash guardat al JSON
            if (password_verify($contrasenya, $usuari['contrasenya'])) {
                
                // 5. ✅ Èxit! Contrasenya correcta.
                
                // 🧱 Bona pràctica: Regenerem l'ID de la sessió
                // per a evitar atacs de "session hijacking".
                session_regenerate_id(true);

                // Guardem les dades de l'usuari a la sessió PHP
                $_SESSION['user_id'] = $usuari['id'];
                $_SESSION['nom_usuari'] = $usuari['nom_usuari'];
                $_SESSION['nom_complet'] = $usuari['nom'] . ' ' . $usuari['cognoms'];

                // 🍪 Guardem la cookie d'identificació (com demanen requisits)
                // La cookie expira en 1 hora (3600 segons)
                // El path "/" fa que estiga disponible en tot el domini.
                setcookie('user_id', $usuari['id'], time() + 3600, "/");

                // 6. Redirigim l'usuari a la seua pàgina de perfil
                header('Location: profile.php');
                exit; // Importantíssim: aturar l'execució després de redirigir

            } else {
                // Contrasenya incorrecta
                $error_message = "Nom d'usuari o contrasenya incorrectes.";
            }
        } else {
            // Usuari no trobat
            // (Donem el mateix missatge per seguretat, per a no donar pistes)
            $error_message = "Nom d'usuari o contrasenya incorrectes.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inici de Sessió - E-Commerce</title>
    <link rel="stylesheet" href="../styles/styleIndex.css">

    <style>
        /* Estils bàsics per als missatges (pots afegir-ho al teu CSS) */
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Estils bàsics per al formulari (similars a register.php) */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h1 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
        }
        .submit-btn:hover {
            background-color: #0056b3;
        }
        .register-link {
            text-align: center;
            margin-top: 1rem;
            display: block;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h1>🔑 Inici de Sessió</h1>

        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="nom_usuari">Nom d'usuari:</label>
                <input type="text" id="nom_usuari" name="nom_usuari" required>
            </div>
            <div class="form-group">
                <label for="contrasenya">Contrasenya:</label>
                <input type="password" id="contrasenya" name="contrasenya" required>
            </div>
            
            <button type="submit" class="submit-btn">Inicia Sessió</button>
        </form>
        
        <a href="register.php" class="register-link">No tens un compte? Registra't</a>
    </div>

</body>
</html>