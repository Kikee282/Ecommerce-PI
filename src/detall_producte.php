<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';

// --- 1. VARIABLES PER A JS ---
$userId = $isLoggedIn ? json_encode($_SESSION['user_id']) : 'null';
$jsUserName = $isLoggedIn ? json_encode($nombreUsuario) : 'null';
$jsUserRole = $isLoggedIn ? json_encode($_SESSION['user_role'] ?? 'user') : '"guest"';
// -----------------------------

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$prodId = $_GET['id'];

// --- 2. CONNEXIÓ AMB L'API (CORREGIDA) ---
// Canviem cURL per file_get_contents que és més estable en el teu entorn
$apiUrl = "http://jsonserver:3000/productes/" . $prodId;

// La @ silencia errors PHP (warnings) per poder gestionar-los nosaltres
$response = @file_get_contents($apiUrl);
$producte = json_decode($response, true);

// Si no hi ha resposta o el JSON no és vàlid, mostrem error
if ($response === false || !$producte) {
    die("<div style='text-align:center; padding:50px;'>
            <h2>Producte no trobat</h2>
            <p>No s'ha pogut connectar amb el servidor o l'ID no existeix.</p>
            <a href='productos.php'>Tornar al catàleg</a>
         </div>");
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producte['nom']); ?> - Detall</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./styles/stylesDetalle.css">
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
                    <a href="./auth/profile.php" style="font-weight: bold;">Hola, <?php echo htmlspecialchars($nombreUsuario); ?></a>
                    <a href="./auth/logout.php" style="color: red;">Tancar Sessió</a>
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
                    
                    <div class="like-container" style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <button id="btnLike" class="btn-like" onclick="toggleLike()" style="background:none; border:none; cursor:pointer; font-size:1.5rem; color:#ccc;">
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
        const currentUser = {
            id: <?php echo $userId; ?>,
            nom: <?php echo $jsUserName; ?>,
            role: <?php echo $jsUserRole; ?>
        };
    </script>

    <script src="./js/likes.js?v=<?php echo time(); ?>"></script>
    <script src="./js/comentarios.js?v=<?php echo time(); ?>"></script>
</body>
</html>