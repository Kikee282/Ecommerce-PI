<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';


// --- LÍNEAS QUE FALTABAN (CRUCIALES PARA EL JS) ---
// Definimos las variables para pasarlas al script de abajo sin errores
$userId = $isLoggedIn ? json_encode($_SESSION['user_id']) : 'null';
// Usamos json_encode para que el string sea seguro en JS (comillas, etc.)
$jsUserName = $isLoggedIn ? json_encode($nombreUsuario) : 'null';
// --------------------------------------------------

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$prodId = $_GET['id'];

// API Request para obtener info del producto
$apiUrl = "http://jsonserver:3000/productes/" . $prodId;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$producte = json_decode($response, true);

if ($httpCode === 404 || !$producte) {
    die("Producte no trobat.");
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producte['nom']); ?> - Detall</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./styles/stylesDetalle.css?v=<?php echo time(); ?>">
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
                    <a href="./auth/profile.php" style="font-weight: bold;"><?php echo htmlspecialchars($nombreUsuario); ?></a>
                    <a href="logout.php" style="color: red;">Tancar Sessió</a>
                <?php else: ?>
                    <a href="./auth/login.html">Iniciar Sessió</a>
                <?php endif; ?>
            </nav>

            <div class="header-icons-clean">
                <?php if ($isLoggedIn): ?>
                    <a href="./auth/profile.php"><i class="fas fa-user"></i></a>
                <?php else: ?>
                    <a href="./auth/login.html"><i class="fas fa-user"></i></a>
                <?php endif; ?>
                <a href="#"><i class="fas fa-shopping-basket"></i></a>
            </div>
        </div>
    </header>

    <main>
        <div class="detail-wrapper">
            
            <div class="product-detail-card">
                <div class="detail-image">
                    <img src="<?php echo htmlspecialchars($producte['img'] ?? './contenido/image.png'); ?>" alt="Imatge del producte">
                </div>
                
                <div class="detail-info">
                    <h1 class="detail-title"><?php echo htmlspecialchars($producte['nom']); ?></h1>
                    <div class="like-container" style="margin-bottom: 20px;">
                        <button id="btnLike" class="btn-like" onclick="toggleLike()">
                            <i class="far fa-heart"></i> </button>
                        <span id="likeCount">0</span> M'agrada
                    </div>
                    <p class="detail-sku">REF: <?php echo htmlspecialchars($producte['sku'] ?? 'GENERIC'); ?></p>
                    <div class="detail-price"><?php echo htmlspecialchars($producte['preu']); ?> €</div>
                    <div class="detail-desc">
                        <p><?php echo htmlspecialchars($producte['descripcio']); ?></p>
                    </div>
                    <p>Estoc disponible: <strong><?php echo $producte['estoc']; ?></strong></p>
                    <button class="btn-add-cart" onclick="alert('Afegit al carret!')">Afegir al Carret</button>
                </div>
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

    <script>
        const currentProductId = <?php echo $prodId; ?>;
        // Ahora sí funcionará porque las variables PHP existen
        const currentUser = {
            id: <?php echo $userId; ?>,
            nom: <?php echo $jsUserName; ?>
        };
    </script>
    
    <script src="./js/likes.js?v=<?php echo time(); ?>"></script>
    <script src="./js/comentarios.js?v=<?php echo time(); ?>"></script>
</body>
</html>