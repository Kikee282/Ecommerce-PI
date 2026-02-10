<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Per L'Art - Joieria</title>
    
    <link href="./styles/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="/styles/styleIndex.css?v=<?php echo time(); ?>">

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
        <section class="hero">
            <div class="hero-content text-center text-lg-start"> <h1>Descobreix Peces Úniques</h1>
                <p>La nostra nova col·lecció inspirada en la cultura popular.</p>
                <a href="productos.php" class="btn btn-primary">Explora la Col·lecció</a>
            </div>
            <div class="hero-background-image"></div>
        </section>

        <section class="featured-products">
            <div class="container">
                <h2>Novetats</h2>
                
<div class="row row-cols-2 row-cols-md-3 g-3 g-lg-4 justify-content-center">                    
                    <div class="col">
                        <div class="product-card mx-auto" style="max-width: 350px;"> 
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/anell_mod.jpg" alt="Anell Espiral">
                                <h3>Anell Espiral</h3>
                            </a>
                            <p class="price">14,00 €</p>
                            <button class="btn btn-icon" aria-label="Afegir al carret Anell Espiral"><i class="fas fa-shopping-cart"></i></button>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="product-card mx-auto" style="max-width: 350px;">
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/collarlibelula_mod.jpg" alt="Collaret Llibèl·lula">
                                <h3>Collaret Llibèl·lula</h3>
                            </a>
                            <p class="price">12,00 €</p>
                            <button class="btn btn-icon" aria-label="Afegir al carret Collaret Llibèl·lula"><i class="fas fa-shopping-cart"></i></button>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="product-card mx-auto" style="max-width: 350px;">
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/brasalet_mod.jpg" alt="Braçalet Dorat">
                                <h3>Braçalet Dorat</h3>
                            </a>
                            <p class="price">54,00 €</p>
                            <button class="btn btn-icon" aria-label="Afegir al carret Braçalet Dorat"><i class="fas fa-shopping-cart"></i></button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="featured-categories">
            <div class="container">
                <h2>Categories Destacades</h2>
                
                <div class="row row-cols-2 row-cols-md-3 g-3 g-lg-4 justify-content-center">
                    
                    <div class="col">
                        <div class="category-item">
                            <a class="d-block text-decoration-none text-reset">
                                <img src="./contenido/anellcoleccio_mod.jpg" alt="Veure col·lecció d'Anells">
                                <h3>Anells</h3>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="category-item">
                            <a class="d-block text-decoration-none text-reset">
                                <img src="./contenido/piercingcoleccio_mod.jpg" alt="Veure col·lecció de Piercings">
                                <h3>Piercings</h3>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="category-item">
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/pulserescoleccio_mod.jpg" alt="Veure col·lecció de Pulseres">
                                <h3>Pulseres</h3>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <div class="container footer-grid">
            <div class="footer-logo">
                <a href="#"><img src="./contenido/log_blanc.png" alt="Logo"></a>
            </div>
            <div class="footer-column">
    <h4>Informació</h4>
    <a href="#">Informació legal</a>
    <a href="#">Política de devolucions</a>
    <a href="#">Política de cookies</a>
    
    <button id="btn-accesibilidad" onclick="toggleAccessibility()">
        <i class="fas fa-universal-access"></i> Mode Llegible / Alt Contrast
    </button>
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

    <script src="./js/bootstrap.bundle.min.js"></script>
    <script>
        if (localStorage.getItem('accessibilityMode') === 'active') {
            document.body.classList.add('accessibility-mode');
        }

        function toggleAccessibility() {
            const body = document.body;
            body.classList.toggle('accessibility-mode');
            
            if (body.classList.contains('accessibility-mode')) {
                localStorage.setItem('accessibilityMode', 'active');
            } else {
                localStorage.removeItem('accessibilityMode');
            }
        }

        
        // 1. Función para cambiar el tamaño
        function setFontSize(size) {
            const html = document.documentElement; // Seleccionamos la etiqueta <html>
            let percentage = '100%'; // Valor por defecto (16px)

            switch (size) {
                case 'small':
                    percentage = '85%'; // ~13.6px
                    break;
                case 'normal':
                    percentage = '100%'; // 16px
                    break;
                case 'large':
                    percentage = '120%'; // ~19.2px
                    break;
                case 'xlarge':
                    percentage = '140%'; // ~22.4px
                    break;
            }

            html.style.fontSize = percentage;
            
            // Guardamos la preferencia
            localStorage.setItem('userFontSize', size);
        }

        // 2. Cargar la preferencia al iniciar la página
        const savedSize = localStorage.getItem('userFontSize');
        if (savedSize) {
            setFontSize(savedSize);
        }
    </script>
</body>
</html>