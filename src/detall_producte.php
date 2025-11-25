<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';

// 1. Validar que tenim un ID
if (!isset($_GET['id'])) {
    header("Location: productos.php");
    exit;
}

$prodId = $_GET['id'];

// 2. Preparar la connexió amb l'API
$apiUrl = "http://jsonserver:3000/productes/" . $prodId;

// 3. Executar la petició al servidor (CURL)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5); 
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch); 
curl_close($ch);

// 4. Intentar llegir les dades
$producte = json_decode($response, true);

// --- BLOC DE DEPURACIÓ (DEBUG) ---
// Si alguna cosa va malament, mostrem les dades tècniques aquí
if ($httpCode !== 200 || empty($producte)) {
    echo "<div style='background: #ffe6e6; border: 1px solid red; padding: 20px; margin: 20px; font-family: monospace;'>";
    echo "<h2 style='color: red; margin-top: 0;'>⚠️ Error de Depuració</h2>";
    echo "<strong>ID Sol·licitat:</strong> " . htmlspecialchars($prodId) . "<br>";
    echo "<strong>URL API Cridada:</strong> " . htmlspecialchars($apiUrl) . "<br>";
    echo "<strong>Codi HTTP Resposta:</strong> " . $httpCode . "<br>";
    echo "<strong>Error cURL (Xarxa):</strong> " . ($curlError ? $curlError : "Cap") . "<br>";
    echo "<strong>Resposta RAW del Servidor:</strong><br>";
    echo "<pre style='background: #fff; padding: 10px; border: 1px solid #ccc;'>" . htmlspecialchars($response ?? '') . "</pre>";
    echo "<br><a href='productos.php'>Tornar enrere</a>";
    echo "</div>";
    die(); // Aturem la pàgina aquí per veure l'error
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
                <a href="./contacte.php">Contacte</a>
                <?php if ($isLoggedIn): ?>
                    <a href="profile.php" style="font-weight: bold;"><?php echo htmlspecialchars($nombreUsuario); ?></a>
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
                // Nota: Des del navegador client, accedim a 'localhost', no 'jsonserver'
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
                container.innerHTML = '<p style="color:red;">Error carregant comentaris. Revisa que el JSON Server funcioni al port 3000.</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', carregarComentaris);
    </script>
</body>
</html>