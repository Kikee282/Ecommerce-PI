<?php
/**
 * auth/register.php
 * * 🧾 Formulari i procés de registre d’usuaris.
 * S'encarrega de validar les dades i enviar-les al JSON Server.
 */

// Incloem l'ajudant de connexió amb JSON Server
require_once '../includes/json_connect.php';

// Iniciem la sessió, encara que en el registre només la fem servir
// per a passar missatges d'error o èxit.
session_start();

$error_message = '';
$success_message = '';

// Comprovem si s'ha enviat el formulari (mètode POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Recollir i netejar les dades del formulari
    // (Usem trim per eliminar espais en blanc a l'inici i al final)
    $nom_usuari = trim($_POST['nom_usuari'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contrasenya = trim($_POST['contrasenya'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $cognoms = trim($_POST['cognoms'] ?? '');

    // 2. Validació de dades del costat servidor
    if (empty($nom_usuari) || empty($email) || empty($contrasenya) || empty($nom) || empty($cognoms)) {
        $error_message = "Tots els camps són obligatoris.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "El format del correu electrònic no és vàlid.";
    } else {
        // 3. Comprovar si l'usuari ja existeix
        $user_exists = findUserByUsername($nom_usuari);
        
        if ($user_exists !== null) {
            $error_message = "El nom d'usuari '$nom_usuari' ja està registrat.";
        } else {
            // 4. Si tot és correcte, xifrem contrasenya i preparem dades
            
            // 🔐 Xifrat de la contrasenya
            $contrasenya_hash = password_hash($contrasenya, PASSWORD_DEFAULT);
            
            // 📤 Dades a enviar al JSON Server
            $data = [
                "nom_usuari" => $nom_usuari,
                "contrasenya" => $contrasenya_hash,
                "email" => $email,
                "nom" => $nom,
                "cognoms" => $cognoms,
                "data_registre" => date('c') // Data en format ISO 8601 (p.ex: 2025-11-07T15:02:00+01:00)
            ];

            // 5. Intentar crear l'usuari via API
            $nou_usuari = createUser($data);

            if ($nou_usuari !== null) {
                // Èxit!
                $success_message = "Registre completat amb èxit! Ara pots iniciar sessió.";
                // Podríem redirigir a login.php
                // header('Location: login.php?status=success');
                // exit;
            } else {
                // Error de connexió amb el JSON Server
                $error_message = "Error: No s'ha pogut completar el registre. Intenta-ho més tard.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre d'Usuari - E-Commerce</title>
    <link rel="stylesheet" href="../styles/styleIndex.css">
    
    <script src="../validacion.js" defer></script>
    
    <style>
        /* Estils bàsics per als missatges (pots afegir-ho al teu CSS) */
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Estils bàsics per al formulari (pots moure'ls al teu CSS) */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .register-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        .register-container h1 {
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
            box-sizing: border-box; /* Perquè el padding no afecti l'amplada */
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
        .login-link {
            text-align: center;
            margin-top: 1rem;
            display: block;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h1>📝 Registre d'Usuari</h1>

        <?php if (!empty($success_message)): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" id="registerForm">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="cognoms">Cognoms:</label>
                <input type="text" id="cognoms" name="cognoms" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="nom_usuari">Nom d'usuari:</label>
                <input type="text" id="nom_usuari" name="nom_usuari" required>
            </div>
            <div class="form-group">
                <label for="contrasenya">Contrasenya:</label>
                <input type="password" id="contrasenya" name="contrasenya" required>
            </div>
            
            <button type="submit" class="submit-btn">Registrar-se</button>
        </form>
        
        <a href="login.php" class="login-link">Ja tens un compte? Inicia sessió</a>
    </div>

</body>
</html>