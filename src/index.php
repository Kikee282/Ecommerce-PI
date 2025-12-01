<?php
// 1. Iniciem la sessió per a poder llegir les variables
session_start();

// 2. Comprovem si l'usuari està loguejat
// Aquesta variable serà TRUE o FALSE. No redirigim a ningú.
$isLoggedIn = isset($_SESSION['user_id']);

// Si està loguejat, agafem el seu nom real (o el nom d'usuari)
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Per L'Art - Joieria</title>
    
    <link rel="stylesheet" href="/styles/styleIndex.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./styles/common.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        <section class="hero">
            <div class="hero-content">
                <h1>Descobreix Peces Úniques</h1>
                <p>La nostra nova col·lecció inspirada en la cultura popular.</p>
                <a href="#" class="btn btn-primary">Explora la Col·lecció</a>
            </div>
            <div class="hero-background-image"></div>
        </section>

        <section class="featured-products">
            <div class="container">
                <h2>Novetats</h2>
                <div class="product-grid">
                    
                    <div class="product-card">
                        <img src="./contenido/anell_mod.jpg" alt="Anell Espiral">
                        <h3>Anell Espiral</h3>
                        <p class="price">14,00 €</p>
                        <button class="btn btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                    
                    <div class="product-card">
                        <img src="./contenido/collarlibelula_mod.jpg" alt="Collaret Llibèl·lula">
                        <h3>Collaret Llibèl·lula</h3>
                        <p class="price">12,00 €</p>
                        <button class="btn btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                    
                    <div class="product-card">
                        <img src="./contenido/brasalet_mod.jpg" alt="Braçalet Dorat">
                        <h3>Braçalet Dorat</h3>
                        <p class="price">54,00 €</p>
                        <button class="btn btn-icon"><i class="fas fa-shopping-cart"></i></button>
                    </div>

                </div>
            </div>
        </section>

        <section class="featured-categories">
            <div class="container">
                <h2>Categories Destacades</h2>
                <div class="category-grid">
                    
                    <div class="category-item">
                        <img src="./contenido/anellcoleccio_mod.jpg" alt="Anells">
                        <h3>Anells</h3>
                    </div>
                    
                    <div class="category-item">
                        <img src="./contenido/piercingcoleccio_mod.jpg" alt="Piercings">
                        <h3>Piercings</h3>
                    </div>
                    
                    <div class="category-item">
                        <img src="./contenido/pulserescoleccio_mod.jpg" alt="Pulseres">
                        <h3>Pulseres</h3>
                    </div>

                </div>
            </div>
        </section>
    </main>

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

</body>
</html>