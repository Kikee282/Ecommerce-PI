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

    <link href="./styles/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm py-3">
        <div class="container-fluid px-4">
            
            <a class="navbar-brand" href="index.php">
                <img src="./contenido/logoParteArriba.png" alt="Logo" style="height: 50px;">
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="menuPrincipal">
                
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center align-items-center">
                    <li class="nav-item"><a class="nav-link" href="productos.php">Productes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Sobre nosaltres</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacte.php">Contacte</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="fontSizeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-font"></i> <span class="d-lg-none ms-2">Mida de la lletra</span>
                        </a>
                        <ul class="dropdown-menu text-center text-lg-start" aria-labelledby="fontSizeDropdown">
                            <li><button class="dropdown-item" onclick="setFontSize('small')">Petita</button></li>
                            <li><button class="dropdown-item" onclick="setFontSize('normal')">Normal</button></li>
                            <li><button class="dropdown-item" onclick="setFontSize('large')">Gran</button></li>
                            <li><button class="dropdown-item" onclick="setFontSize('xlarge')">Molt Gran</button></li>
                        </ul>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-center align-items-center gap-2">
                    
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link fw-bold" href="./auth/profile.php">
                                <?php echo htmlspecialchars($nombreUsuario); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="./auth/logout.php">
                                Tancar Sessió
                            </a>
                        </li>

                    <?php endif; ?>

                    <li class="nav-item d-flex align-items-center justify-content-center gap-3 mt-2 mt-lg-0">
                        
                        <?php if ($isLoggedIn): ?>
                            <a class="nav-link p-0" href="./auth/profile.php">
                                <i class="fas fa-user fs-5"></i>
                            </a>
                        <?php else: ?>
                            <a class="nav-link p-0" href="./auth/login.html">
                                <i class="fas fa-user fs-5"></i>
                            </a>
                        <?php endif; ?>

                        <a class="nav-link p-0" href="#">
                            <i class="fas fa-shopping-basket fs-5"></i>
                        </a>
                    </li>

                </ul>
                
            </div>
        </div>
    </nav>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/validacion.js"></script>
</body>
</html>