<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';

if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$prodId = $_GET['id'];

// API Request
$apiUrl = "http://jsonserver:3000/productes/" . $prodId;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
    
    <link rel="stylesheet" href="./styles/stylesDetalle.css">
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
                <a href="#contacte">Contacte</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" style="font-weight: bold;">Hola, <?php echo htmlspecialchars($nombreUsuario); ?></a>
                <?php else: ?>
                    <a href="login.html">Iniciar Sessió</a>
                <?php endif; ?>
            </nav>

            <div class="header-icons-clean">
                <a href="profile.php"><i class="fas fa-user"></i></a>
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
                    <p class="detail-sku">REF: <?php echo htmlspecialchars($producte['sku'] ?? 'GENERIC'); ?></p>
                    
                    <div class="detail-price"><?php echo htmlspecialchars($producte['preu']); ?> €</div>
                    
                    <div class="detail-desc">
                        <p><?php echo htmlspecialchars($producte['descripcio']); ?></p>
                    </div>
                    
                    <p>Estoc disponible: <strong><?php echo $producte['estoc']; ?></strong></p>
                    
                    <button class="btn-add-cart" onclick="alert('Afegit al carret!')">
                        Afegir al Carret
                    </button>
                </div>
            </div>

            <div class="comments-section">
                <h2>Comentaris</h2>
                <div id="llista-comentaris">
                    <p>Carregant comentaris...</p>
                </div>
            </div>

        </div>
    </main>

    <script>
        const currentProductId = <?php echo $prodId; ?>;

        async function carregarComentaris() {
            const container = document.getElementById('llista-comentaris');
            try {
                const response = await fetch(`http://localhost:3000/comentaris?productId=${currentProductId}`);
                if (!response.ok) throw new Error('Error de xarxa');
                const comentaris = await response.json();

                container.innerHTML = '';

                if (comentaris.length === 0) {
                    container.innerHTML = '<p style="color:#777; font-style:italic;">Encara no hi ha comentaris per a aquest producte.</p>';
                    return;
                }

                comentaris.forEach(c => {
                    const dataFormatada = new Date(c.data).toLocaleDateString('ca-ES');
                    const div = document.createElement('div');
                    div.className = 'comment';
                    div.innerHTML = `
                        <div class="comment-header">
                            ${c.nom_usuari} <span class="comment-date">${dataFormatada}</span>
                        </div>
                        <div class="comment-body">
                            ${c.text}
                        </div>
                    `;
                    container.appendChild(div);
                });

            } catch (error) {
                console.error('Error:', error);
                container.innerHTML = '<p style="color:red;">Error carregant comentaris.</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', carregarComentaris);
    </script>
</body>
</html>