<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';

// API
$apiUrl = "http://jsonserver:3000/productes";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
$jsonResponse = curl_exec($ch);
curl_close($ch);
$productes = json_decode($jsonResponse, true);
if (!$productes) $productes = [];
?>

<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productes - Per L’Art</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <link rel="stylesheet" href="./styles/styleIndex.css">
  
  <link rel="stylesheet" href="./styles/stylesProductes.css">
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
<div class="catalog-container">
        <h1 class="page-title">Tots els productes:</h1>

        <section class="showcase" id="lista-productos">
            <p style="text-align:center; width: 100%; color: #666;">Carregant productes...</p>
        </section>
    </div>
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
    </main>
    <script src="./js/productos.js"></script>
</body>
</html>