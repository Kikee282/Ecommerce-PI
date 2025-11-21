<?php
session_start();

// Dades de l'usuari per al Header
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';

// --- 1. OBTENIR PRODUCTES DEL JSON SERVER (API) ---
$apiUrl = "http://jsonserver:3000/productes";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$jsonResponse = curl_exec($ch);
curl_close($ch);

$productes = json_decode($jsonResponse, true);

if (!$productes) $productes = [];
?>

<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productes - Per L’Art</title>
  <link rel="stylesheet" href="./styles/styleIndex.css">
  <style>
    /* --- ESTILS ESPECÍFICS PER A LA GRAELLA DE PRODUCTES --- */
    
    /* Convertim el contenidor en una graella (Grid) */
    .showcase {
        display: grid !important; /* !important per sobreescriure styleIndex.css */
        /* 3 columnes d'igual amplada. Es redueix automàticament en pantalles petites */
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
        gap: 40px;
        align-items: start; /* Alineem els elements a dalt */
        width: 90%; /* Una mica més ample per aprofitar l'espai */
        max-width: 1200px;
    }

    /* Estil de cada targeta de producte */
    .producte {
        display: flex !important;
        flex-direction: column !important; /* Imatge dalt, text baix */
        align-items: center;
        text-align: center;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05); /* Ombra suau */
        transition: transform 0.2s;
        width: 100%;
        height: 70%; /* Perquè totes les targetes tinguin la mateixa alçada */
    }

    /* Efecte en passar el ratolí */
    .producte:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }

    /* Sobreescrivim la regla de "files alternes" del CSS original */
    .producte:nth-child(even) {
        flex-direction: column !important;
    }

    /* Imatges */
    .producte img {
        width: 100%;
        height: 250px; /* Alçada fixa */
        object-fit: cover; /* Retalla la imatge sense deformar-la */
        border-radius: 8px;
        margin-bottom: 15px;
    }

    /* Títol del producte */
    .producte h3 {
        font-size: 1.2rem;
        margin: 10px 0;
        color: #333;
    }

    /* Descripció */
    .producte p {
        font-size: 0.95rem;
        color: #666;
        line-height: 1.5;
        flex-grow: 1; /* Empeny el preu i el botó cap avall */
        text-align: center !important; /* Forcem text centrat */
    }

    .price-tag {
        font-weight: bold;
        color: #243020;
        font-size: 1.3em;
        margin: 15px 0;
    }

    .btn {
        width: 100%; /* Botó ocupa tot l'ample de la targeta */
    }
  </style>
</head>
<body>

  <header>
    <div class="top-deco"></div>
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
    <div class="logoInicio">
        <img src="./contenido/logo.png" alt="Logo Per L’Art">
    </div>
  </header>

  <main>
    <section class="intro">
      <h1>El Nostre Catàleg</h1>
      <p>Peces úniques fetes a mà amb amor i dedicació.</p>
    </section>

    <section class="showcase" id="productes">
      
      <?php if (empty($productes)): ?>
          <p style="text-align: center; grid-column: 1 / -1;">No hi ha productes disponibles en aquest moment.</p>
      <?php else: ?>
          
          <?php foreach ($productes as $prod): ?>
            <div class="producte">
                <a href="detall_producte.php?id=<?php echo $prod['id']; ?>">
                    <img src="<?php echo htmlspecialchars($prod['img'] ?? './contenido/image.png'); ?>" alt="<?php echo htmlspecialchars($prod['nom']); ?>">
                </a>
                
                <a href="detall_producte.php?id=<?php echo $prod['id']; ?>" style="text-decoration: none; color: inherit;">
                    <h3><?php echo htmlspecialchars($prod['nom']); ?></h3>
                </a>
                <p><?php echo htmlspecialchars($prod['descripcio']); ?></p>
                <div class="price-tag"><?php echo htmlspecialchars($prod['preu']); ?> €</div>
                
                <?php if ($prod['estoc'] > 0): ?>
                    <button class="btn" onclick="afegirAlCarret(<?php echo $prod['id']; ?>)">Afegir al Carret</button>
                <?php else: ?>
                    <p style="color: red; font-weight: bold;">Esgotat</p>
                <?php endif; ?>
            </div>
          <?php endforeach; ?>

      <?php endif; ?>

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

  <script>
      function afegirAlCarret(idProducte) {
          alert("Producte " + idProducte + " afegit al carret (Simulació)");
      }
  </script>

</body>
</html>