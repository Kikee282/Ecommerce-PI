<?php
session_start();

// Lógica básica para saber si hay usuario (Ajusta 'user_id' según uses en login.php)
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuari';
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosaltres - Per L'Art</title>
    
    <link href="./styles/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="./styles/common.css">
    
    <link rel="stylesheet" href="./styles/stylesAbout.css">

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
                    <li class="nav-item"><a class="nav-link" href="sobre_nosaltres.php">Sobre nosaltres</a></li>
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
                            <a class="nav-link p-0" href="./auth/profile.php"><i class="fas fa-user fs-5"></i></a>
                        <?php else: ?>
                            <a class="nav-link p-0" href="./auth/login.html"><i class="fas fa-user fs-5"></i></a>
                        <?php endif; ?>
                        <a class="nav-link p-0" href="#"><i class="fas fa-shopping-basket fs-5"></i></a>
                    </li>
                </ul>
                
            </div>
        </div>
    </nav>

    <main>
        
        <section class="about-hero bg-light">
            <div class="container">
                <h1>La Nostra Essència</h1>
                <p>Artesania, disseny i passió per les joies úniques des de 2026.</p>
            </div>
        </section>

        <section class="about-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <img src="./contenido/anell.jpg" alt="Joieria Artesanal" class="about-img">
                    </div>
                    <div class="col-lg-6">
                        <div class="about-content">
                            <h2>Més que joies, històries</h2>
                            <p>
                                A Per L'Art creiem que cada peça de joieria explica una història. 
                                El nostre viatge va començar amb la idea de fusionar l'artesania tradicional 
                                amb dissenys contemporanis, creant peces que no només es porten, sinó que se senten.
                            </p>
                            <p>
                                Treballem amb els millors materials, seleccionats amb cura, per garantir 
                                que cada anell, collaret i polsera sigui una obra d'art duradora.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-section bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2>Els Nostres Valors</h2>
                </div>
                
                <div class="values-grid">
                    <div class="value-card">
                        <i class="fas fa-gem"></i>
                        <h4>Qualitat Premium</h4>
                        <p>Materials autèntics i acabats perfectes en cada detall.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-leaf"></i>
                        <h4>Sostenibilitat</h4>
                        <p>Compromesos amb processos ètics i respectuosos amb el medi ambient.</p>
                    </div>
                    <div class="value-card">
                        <i class="fas fa-heart"></i>
                        <h4>Passió</h4>
                        <p>Dissenyem cada peça amb l'amor i la dedicació que es mereix.</p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include 'footer.php'; ?>

</body>
</html>