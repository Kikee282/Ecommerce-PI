<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';

// 1. VARIABLES PARA JS
$userId = $isLoggedIn ? json_encode($_SESSION['user_id']) : 'null';
$jsUserName = $isLoggedIn ? json_encode($nombreUsuario) : 'null';
$jsUserRole = $isLoggedIn ? json_encode($_SESSION['user_role'] ?? 'user') : '"guest"';

// 2. VALIDACIÓN DE ID
if (empty($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$prodId = $_GET['id'];

// 3. CONEXIÓN SEGURA (CORREGIDA)
// Usamos barra / para obtener el objeto directo, no un array
$apiUrl = "http://jsonserver:3000/productes/" . $prodId;

$json = @file_get_contents($apiUrl);
$producte = json_decode($json, true);

// 4. COMPROBACIÓN
if ($json === false || !$producte) {
    // Si falla, mostramos error claro
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h2 style='color:#d9534f'>Producte no trobat</h2>
            <p>No s'ha trobat cap producte amb ID: " . htmlspecialchars($prodId) . "</p>
            <a href='productos.php' style='color:blue; text-decoration:underline'>Tornar al catàleg</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producte['nom']); ?> - Detall</title>
        

    <link rel="stylesheet" href="./styles/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./styles/stylesDetalle.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="./styles/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link p-0 position-relative" href="carret.php">
                        <i class="fas fa-shopping-basket fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" style="font-size: 0.6rem; display:none;">
                            0
                        </span>
                    </a>
                    </a>
                </li>

            </ul>
            
        </div>
    </div>
</nav>

    <main>
        <div class="detail-wrapper">
            
            <div class="product-detail-card">
                <div class="detail-image">
                    <img src="<?php echo htmlspecialchars($producte['img'] ?? './contenido/image.png'); ?>" alt="Imatge del producte">
                </div>
                
                <div class="detail-info">
                    <h1 class="detail-title"><?php echo htmlspecialchars($producte['nom']); ?></h1>
                    
                    <div class="like-container" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <button id="btnLike" class="btn-like" onclick="toggleLike()">
                            <i class="far fa-heart"></i>
                        </button>
                        <span style="font-size: 0.9rem; color: #666;">
                            <span id="likeCount">0</span> persones els agrada
                        </span>
                    </div>

                    <p class="detail-sku">REF: <?php echo htmlspecialchars($producte['sku'] ?? 'GENERIC'); ?></p>
                    <div class="detail-price"><?php echo htmlspecialchars($producte['preu']); ?> €</div>
                    <div class="detail-desc">
                        <p><?php echo htmlspecialchars($producte['descripcio']); ?></p>
                    </div>
                    <p>Estoc disponible: <strong><?php echo $producte['estoc']; ?></strong></p>
                    
                <button class="btn-add-cart" onclick="afegirProducteActual()">Afegir al Carret</button>                </div>
            </div>

            <div class="comments-section">
                <h2>Comentaris</h2>

                <?php if ($isLoggedIn): ?>
                    <div class="comment-form-container">
                        <h3>Deixa la teva opinió</h3>
                        <form id="formComentari">
                            <div class="form-row">
                                <label for="puntuacio">Valoració:</label>
                                <select id="puntuacio" class="select-rating">
                                    <option value="5">★★★★★ (Excel·lent)</option>
                                    <option value="4">★★★★ (Molt bo)</option>
                                    <option value="3">★★★ (Correcte)</option>
                                    <option value="2">★★ (Regular)</option>
                                    <option value="1">★ (Dolent)</option>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <label for="textComentari">Comentari:</label>
                                <textarea id="textComentari" class="input-comment" placeholder="Escriu aquí..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn-submit-comment">Publicar</button>
                        </form>
                    </div>
                <?php else: ?>
                    <div class="login-notice">
                        <p><a href="./auth/login.html">Inicia sessió</a> per a deixar un comentari.</p>
                    </div>
                <?php endif; ?>

                <div id="llista-comentaris">
                    <p>Carregant comentaris...</p>
                </div>
            </div>

        </div>
    </main>

    <?php include 'footer.php'; ?>

<script>
    // Usamos json_encode para asegurar que el ID tenga el formato correcto (número o texto)
    const currentProductId = <?php echo json_encode($prodId); ?>;
    
    // Definimos el usuario con los datos de PHP
    const currentUser = {
        id: <?php echo $userId; ?>,      // Ya viene codificado como json o 'null' desde arriba
        nom: <?php echo $jsUserName; ?>, // Ya viene codificado
        role: <?php echo $jsUserRole; ?> // Ya viene codificado
    };

    // Función para añadir al carrito (usando la variable $producte de PHP)
    function afegirProducteActual() {
        const producto = {
            id: <?php echo json_encode($producte['id']); ?>,
            nom: <?php echo json_encode($producte['nom']); ?>,
            preu: <?php echo json_encode($producte['preu']); ?>,
            img: <?php echo json_encode($producte['img'] ?? './contenido/image.png'); ?>
        };
        
        if (typeof Carrito !== 'undefined') {
            Carrito.add(producto);
            alert('Producte afegit al carret!');
        } else {
            console.error("Error: logicCarret.js no s'ha carregat.");
        }
    }
</script>

<script src="./js/bootstrap.bundle.min.js"></script>
<script src="./js/logicCarret.js?v=<?php echo time(); ?>"></script>

<script src="./js/likes.js?v=<?php echo time(); ?>"></script>
<script src="./js/comentarios.js?v=<?php echo time(); ?>"></script>

</body>
</html>