<?php
/**
 * auth/profile.php
 * * 👤 Mostra i permet editar les dades de l’usuari autenticat.
 * Aquesta pàgina està protegida; només hi poden accedir usuaris
 * amb una sessió activa.
 */

// Incloem l'ajudant de connexió amb JSON Server
require_once '../includes/json_connect.php';

// 1. 🛡️ Iniciem la sessió i protegim la pàgina
session_start();

// Si l'user_id NO està a la sessió, significa que l'usuari
// no ha iniciat sessió. El redirigim a login.php.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit; // Aturem l'execució
}

// Si estem aquí, l'usuari està autenticat.
$user_id = $_SESSION['user_id'];

$error_message = '';
$success_message = '';

// 2. ✏️ Lògica per a ACTUALITZAR dades (si s'envia el formulari)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recollim dades del formulari d'edició
    $nom = trim($_POST['nom'] ?? '');
    $cognoms = trim($_POST['cognoms'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validació bàsica
    if (empty($nom) || empty($cognoms) || empty($email)) {
        $error_message = "Tots els camps (nom, cognoms, email) són obligatoris.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "El format del correu electrònic no és vàlid.";
    } else {
        // Dades a actualitzar (només les que permetem canviar)
        $data_to_patch = [
            "nom" => $nom,
            "cognoms" => $cognoms,
            "email" => $email
        ];

        // Cridem a la funció per actualitzar (que hem d'afegir a json_connect.php)
        $updated_user = updateUser($user_id, $data_to_patch);

        if ($updated_user !== null) {
            $success_message = "Dades actualitzades correctament!";
            // Actualitzem la sessió per si ha canviat el nom
            $_SESSION['nom_complet'] = $updated_user['nom'] . ' ' . $updated_user['cognoms'];
        } else {
            $error_message = "Error en actualitzar les dades. Intenta-ho més tard.";
        }
    }
}

// 3. 📋 Lògica per a MOSTRAR dades (sempre s'executa)
// Cridem a la funció per obtindre les dades (que hem d'afegir a json_connect.php)
$usuari = findUserById($user_id);

// Comprovació de seguretat: si la sessió és vàlida però l'usuari
// ha sigut eliminat del JSON, el fem fora.
if ($usuari === null) {
    header('Location: logout.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil d'Usuari - E-Commerce</title>
    <link rel="stylesheet" href="../styles/styleIndex.css">
    
    <style>
        /* Estils bàsics per als missatges (pots afegir-ho al teu CSS) */
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Estils bàsics per al perfil (similars a register.php) */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .profile-container {
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        .profile-container h1, .profile-container h2 {
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
        .form-group input, .form-group span {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box; 
        }
        .form-group span {
            background-color: #eee;
            color: #555;
            display: inline-block;
        }
        .submit-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
        }
        .submit-btn:hover {
            background-color: #218838;
        }
        .logout-link {
            text-align: center;
            margin-top: 1rem;
            display: block;
            background-color: #dc3545;
            color: white;
            padding: 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        .logout-link:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

    <div class="profile-container">
        <h1>👤 Perfil de <?php echo htmlspecialchars($_SESSION['nom_complet']); ?></h1>

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

        <h2>Edita les teves dades</h2>
        
        <form action="profile.php" method="POST">
            <div class="form-group">
                <label>Nom d'usuari:</label>
                <span><?php echo htmlspecialchars($usuari['nom_usuari']); ?></span>
            </div>
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($usuari['nom']); ?>" required>
            </div>
            <div class="form-group">
                <label for="cognoms">Cognoms:</label>
                <input type="text" id="cognoms" name="cognoms" value="<?php echo htmlspecialchars($usuari['cognoms']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuari['email']); ?>" required>
            </div>
            <div class="form-group">
                <label>Data de registre:</label>
                <span><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($usuari['data_registre']))); ?></span>
            </div>
            
            <button type="submit" class="submit-btn">Actualitzar Dades</button>
        </form>
        
        <a href="logout.php" class="logout-link">🚪 Tanca la sessió</a>
    </div>

</body>
</html>