<?php
// --- 1. INICI DE SESSIÓ (Això arregla el Warning) ---
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';

// Variables del formulari
$name = '';
$email = '';
$message = '';
$errors = [];
$enviado = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars(trim($_POST["name"] ?? ''));
    $email = htmlspecialchars(trim($_POST["email"] ?? ''));
    $message = htmlspecialchars(trim($_POST["message"] ?? ''));
    
    $privacyPolicy = isset($_POST["privacyPolicy"]);
    $skipValidation = isset($_POST["skipValidation"]); 

    if (!$skipValidation) {
        if (empty($name)) $errors[] = "El nom és obligatori.";
        if (empty($email)) $errors[] = "El correu electrònic és obligatori.";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Introdueix un correu electrònic vàlid.";
        if (strlen($message) < 5) $errors[] = "El missatge ha de tindre almenys 5 caràcters.";
        if (!$privacyPolicy) $errors[] = "Has d'acceptar la política de privacitat.";
    }

    if (empty($errors)) {
        $enviado = true;
        // Aquí aniria l'enviament real
    }
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacte - Per L'Art</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./styles/stylesContact.css">
    <link rel="stylesheet" href="./styles/common.css">

</head>
<body>
    <header class="header-exacto">
        <div class="header-logo-container">
            <a href="index.php">
                <img src="./contenido/logoParteArriba.png" alt="Logo">
            </a>
        </div>

        <div class="header-right-side">
            <nav class="nav-links-clean">
                <a href="productos.php">Productes</a>
                <a href="#">Sobre nosaltres</a>
                <a href="contacte.php">Contacte</a>
                <?php if ($isLoggedIn): ?>
                    <a href="./auth/profile.php"><?php echo htmlspecialchars($nombreUsuario); ?></a>
                    <a href="./auth/logout.php" style="color: red;">Tancar Sessió</a>
                    <?php else: ?>
                    <a href="./auth/login.html">Iniciar Sessió</a>
                    <?php endif; ?>
            </nav>
            <?php if ($isLoggedIn): ?>
            <div class="header-icons-clean">
                <a href="./auth/profile.php"><i class="fas fa-user"></i></a>
                <a href="#"><i class="fas fa-shopping-basket"></i></a>
            </div>
            <?php else: ?>
              <div class="header-icons-clean">
                <a href="./auth/login.html"><i class="fas fa-user"></i></a>
                <a href="#"><i class="fas fa-shopping-basket"></i></a>
            </div>
            <?php endif; ?>
        </div>
    </header>
    <main>
        <div class="contact-wrapper">
            
            <?php if ($enviado): ?>
                <div class="success-message">
                    <h3>Missatge enviat correctament!</h3>
                    <p>Gràcies per contactar amb nosaltres.</p>
                    <a href="index.php">Tornar a l'inici</a>
                </div>
            <?php else: ?>
                
                <form id="contactForm" method="POST" action="" novalidate>
                    <h3>Contacta amb nosaltres</h3>
                    
                    <label for="name">Nom *</label>
                    <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>

                    <label for="email">Correu electrònic *</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>

                    <label for="message">Missatge *</label>
                    <textarea id="message" name="message" required><?php echo $message; ?></textarea>

                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <input type="checkbox" id="privacyPolicy" name="privacyPolicy" required <?php if(isset($_POST['privacyPolicy'])) echo "checked"; ?>>
                        <label for="privacyPolicy" style="margin: 0;">He llegit i accepte la política de privacitat *</label>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                        <input type="checkbox" id="skipValidation" name="skipValidation" <?php if(isset($_POST['skipValidation'])) echo "checked"; ?>>
                        <label for="skipValidation" style="margin: 0;">Desactivar validació en client (per a proves)</label>
                    </div>

                    <button type="submit" class="btn">Enviar</button>
                </form>

                <?php if (!empty($errors)): ?>
                    <div class="server-errors">
                        <h3>S'han trobat errors:</h3>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
            <?php endif; ?>
            
        </div>
    </main>
    <footer class="main-footer">
        <div class="container footer-grid">
            
            <div class="footer-logo">
                <a href="./index.php"><img src="./contenido/log_blanc.png" alt="Logo" lin></a>
            </div>
            
            <div class="footer-column">
                <h4>Informació</h4>
                <a href="#">Informació legal</a>
                <a href="#">Política de devolucions</a>
                <a href="#">Política de cookies</a>
            </div>
            
            <div class="footer-column">
                <h4>Contacte</h4>
                <p>Telèfon: 122 884 2887</p>
                <a href="#">Sobre nosaltres</a>
            </div>
            
            <div class="footer-column">
                <h4>Segueix-nos</h4>
                <div class="social-icons">
                    <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="./js/validacion.js"></script>
</body>
</html>