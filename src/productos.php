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
</head>
<body>

  <header class="main-header">
        <div class="header-container">
            <a href="./index.php"><img src="./contenido/logoParteArriba.png"></a>
            
            <nav class="main-nav">
        <ul>
            <a href="./productos.php"><li>Productes</li></a>
            <a href=""><li>Sobre nosaltres</li></a>
            <a href="./contacte.php"><li>Contacte</li></a>
            <?php if ($isLoggedIn): ?>
        
            <li><a href="./auth/profile.php"><?php echo htmlspecialchars($nombreUsuario); ?></li></a>
            
            <li><a href="./auth/logout.php" style="color: red;">Tancar Sessió</a></li>

            <?php else: ?>
            
                <li><a href="./auth/login.html">Iniciar Sessió</a></li>
                
            <?php endif; ?>
        </ul>
        </nav>
            
            <div class="header-icons">
                <a href="./auth/login.html" aria-label="Compte"><i class="fas fa-user"></i></a>
                <a href="#" aria-label="Cesta"><i class="fas fa-shopping-basket"></i></a>
            </div>
        </div>
    </header>

  <main>
    <div class="catalog-container">
        <h1 class="page-title">Todos los productos:</h1>

        <section class="showcase">
        <?php if (empty($productes)): ?>
            <p>No hi ha productes disponibles.</p>
        <?php else: ?>
            <?php foreach ($productes as $prod): ?>
                <div class="producte-minimal">
                    <a href="detall_producte.php?id=<?php echo $prod['id']; ?>">
                        <img src="<?php echo htmlspecialchars($prod['img'] ?? './contenido/image.png'); ?>" alt="<?php echo htmlspecialchars($prod['nom']); ?>">
                    </a>
                    
                    <div class="prod-row-top">
                        <a href="detall_producte.php?id=<?php echo $prod['id']; ?>" class="prod-name">
                            <?php echo htmlspecialchars($prod['nom']); ?>
                        </a>
                        <button class="btn-cart-icon" onclick="afegirAlCarret(<?php echo $prod['id']; ?>)">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>

                    <div class="prod-price"><?php echo htmlspecialchars($prod['preu']); ?>€</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </section>
    </div>
    <footer class="main-footer">
        <div class="container footer-grid">
            
            <div class="footer-logo">
                <a href="#"><img src="./contenido/log_blanc.png" alt="Logo" lin></a>
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
</body>
</html>