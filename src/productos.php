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
    <title>Productes - Per L’Art</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/stylesProductes.css">

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
                    <li class="nav-item"><a class="nav-link active" href="productos.php">Productes</a></li>
                    <li class="nav-item"><a class="nav-link" href="sobre_nosaltres.php">Sobre nosaltres</a></li>
                    <li class="nav-item"><a class="nav-link" href="contacte.php">Contacte</a></li>
                </ul>

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-center align-items-center gap-3">
                    
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="nav-link text-danger" href="./auth/logout.php">Tancar Sessió</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link p-0" href="./auth/profile.php"><i class="fas fa-user fs-5"></i></a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link p-0" href="./auth/login.html"><i class="fas fa-user fs-5"></i></a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item position-relative">
                        <a class="nav-link p-0" href="carret.php">
                            <i class="fas fa-shopping-basket fs-5"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" style="font-size: 0.6rem; display:none;">
                                0
                            </span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <main style="margin-top: 100px;"> <div class="catalog-container container my-5">
            <h1 class="page-title text-left mb-5">Tots els productes:</h1>

            <section class="showcase" id="lista-productos">
                <div class="spinner-border text-primary d-block mx-auto" role="status">
                    <span class="visually-hidden">Carregant...</span>
                </div>
            </section>
        </div>
    </main><?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/logicCarret.js"></script>
    <script src="./js/productos.js"></script>
</body>   
</html>