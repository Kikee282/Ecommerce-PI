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

// 2. Obtenir dades del producte (Server-Side)
// Nota: Fem servir el nom del contenidor 'jsonserver' per comunicació interna PHP->API
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
    <title><?php echo htmlspecialchars($producte['nom']); ?> - Detall</title>
    <link rel="stylesheet" href="./styles/styleIndex.css">
    <style>
        /* Estils per a la fitxa de detall */
        .detail-container {
            max-width: 900px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            display: flex;
            gap: 40px;
        }
        .detail-image img {
            max-width: 400px;
            border-radius: 8px;
            object-fit: cover;
        }
        .detail-info { flex: 1; }
        .price-large { font-size: 2em; color: #243020; font-weight: bold; margin: 20px 0; }
        
        /* Estils per als comentaris */
        .comments-section {
            max-width: 900px;
            margin: 40px auto;
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
        }
        .comment {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .comment-header { font-weight: bold; margin-bottom: 5px; color: #333; }
        .comment-date { font-size: 0.85em; color: #777; font-weight: normal; }
        .comment-body { color: #555; }
    </style>
</head>
<body>

    <header>
        <nav class="navbar">
            <ul>
        <div class="logoHeader">
          <a href="index.php"><img src="./contenido/logoParteArriba.png" alt="Logo Per L’Art"></a>
        </div>   
        <li><a href="productos.php" style="font-weight: bold;">Productes</a></li>
        <li><a href="#">Sobre nosaltres</a></li>
        <li><a href="">Contacte</a></li>

        <?php if ($isLoggedIn): ?>
            <li>Hola, <a href="profile.php"><strong><?php echo htmlspecialchars($nombreUsuario); ?></strong></a></li>
            <li><a href="logout.php" style="color: red;">Sortir</a></li>
        <?php else: ?>
            <li><a href="login.html">Iniciar Sessió</a></li>
        <?php endif; ?>

      </ul>
        </nav>
    </header>

    <main>
        <div class="detail-container">
            <div class="detail-image">
                <img src="<?php echo htmlspecialchars($producte['img'] ?? './contenido/image.png'); ?>" alt="Imatge del producte">
            </div>
            <div class="detail-info">
                <h1><?php echo htmlspecialchars($producte['nom']); ?></h1>
                <div class="price-large"><?php echo htmlspecialchars($producte['preu']); ?> €</div>
                <p><?php echo htmlspecialchars($producte['descripcio']); ?></p>
                <p>Estoc disponible: <?php echo $producte['estoc']; ?></p>
                <button class="btn" onclick="alert('Afegit al carret!')">Afegir al Carret</button>
            </div>
        </div>

        <div class="comments-section">
            <h2>Comentaris i Valoracions</h2>
            <div id="llista-comentaris">
                <p>Carregant comentaris...</p>
            </div>
        </div>
    </main>

    <script>
        // ID del producte actual (passat des de PHP a JS)
        const currentProductId = <?php echo $prodId; ?>;

        // Funció per carregar comentaris via AJAX (Fetch API)
        async function carregarComentaris() {
            const container = document.getElementById('llista-comentaris');
            
            try {
                // 1. Fem la petició al JSON Server
                // Nota: Des del navegador (JS), accedim a 'localhost', no 'jsonserver'
                const response = await fetch(`http://localhost:3000/comentaris?productId=${currentProductId}`);
                
                if (!response.ok) throw new Error('Error de xarxa');
                
                const comentaris = await response.json();

                // 2. Netegem el contenidor
                container.innerHTML = '';

                if (comentaris.length === 0) {
                    container.innerHTML = '<p>Encara no hi ha comentaris per a aquest producte.</p>';
                    return;
                }

                // 3. Generem l'HTML per a cada comentari
                comentaris.forEach(c => {
                    // Format de data simple
                    const dataFormatada = new Date(c.data).toLocaleDateString('ca-ES');
                    
                    const div = document.createElement('div');
                    div.className = 'comment';
                    div.innerHTML = `
                        <div class="comment-header">
                            ${c.nom_usuari} <span class="comment-date">- ${dataFormatada}</span>
                        </div>
                        <div class="comment-body">
                            ${c.text}
                        </div>
                    `;
                    container.appendChild(div);
                });

            } catch (error) {
                console.error('Error carregant comentaris:', error);
                container.innerHTML = '<p style="color:red;">Error carregant els comentaris. Assegura\'t que el JSON Server està funcionant al port 3000.</p>';
            }
        }

        // Executar la càrrega quan la pàgina estigui llesta
        document.addEventListener('DOMContentLoaded', carregarComentaris);
    </script>
</body>
</html>