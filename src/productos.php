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

  <link href="./styles/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/productos.js"></script>
</body>
</html>