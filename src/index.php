<?php
// 1. Iniciem la sessió per a poder llegir les variables
session_start();

// 2. Comprovem si l'usuari està loguejat
// Aquesta variable serà TRUE o FALSE. No redirigim a ningú.
$isLoggedIn = isset($_SESSION['user_id']);

// Si està loguejat, agafem el seu nom real (o el nom d'usuari)
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';
?>

<!DOCTYPE html>
<html lang="ca">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Per l'art</title>
  <link rel="stylesheet" href="./styles/styleIndex.css">
</head>
<body>
  <!-- HEADER -->
  <header>
    <div class="top-deco"></div>
    <nav class="navbar">
      <ul>
        <div class="logoHeader">
          <img src="./contenido/logoParteArriba.png" alt="Logo Per L’Art">
        </div>   
        <li>productes</li>
        <li>Sobre nosaltres</li>
        <li>Contacte</li>
        <?php if ($isLoggedIn): ?>
    
        <li><a href="./auth/profile.php"><?php echo htmlspecialchars($nombreUsuario); ?></li></a>
        
        <li><a href="./auth/logout.php" style="color: red;">Tancar Sessió</a></li>

    <?php else: ?>
    
        <li><a href="./auth/login.html">Iniciar Sessió</a></li>
        
    <?php endif; ?>
      </ul>
    </nav>
    <div class="logoInicio">
      <img src="./contenido/logo.png" alt="Logo Per L’Art">
    </div>
  </header>

  <!-- MAIN -->
  <main>
    <section class="intro">
      <p>Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero, viverra ridiculus eget blandit mattis consequat convallis imperdiet diam.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.</p>
    </section>

    <section class="showcase" id="productes">
      <div class="producte">
        <img src="./contenido/image_anell.png" alt="Anell elegant">
        <p>Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.</p>
      </div>

      <div class="producte">
        <img src="./contenido/image.png" alt="Collaret artístic">
        <p>Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.Lorem ipsum dolor sit amet consectetur adipiscing elit hendrerit vestibulum et libero.</p>
      </div>
    </section>

    <div class="button-center">
      <button class="btn">Productes destacats</button>
    </div>
  </main>

  <!-- FOOTER -->
  <footer>
    <div class="footer-top" id="contacte">
      <form id="contactForm" method="POST" action="./process_form.php" novalidate>
        <h3>Contacta amb nosaltres</h3>
        <label for="name">Nom *</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Correu electrònic *</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Missatge *</label>
        <textarea id="message" name="message" required></textarea>

        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <input type="checkbox" id="privacyPolicy" name="privacyPolicy" required>
          <label for="privacyPolicy" style="margin: 0;">He llegit i accepte la política de privacitat *</label>
        </div>

        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
          <input type="checkbox" id="skipValidation" name="skipValidation">
          <label for="skipValidation" style="margin: 0;">Desactivar validació en client (per a proves)</label>
        </div>

        <button type="submit" class="btn">Enviar</button>
      </form>
    </div>

    <div class="footer-bottom">
      <div class="footer-section">
        <p>Informació legal</p>
        <p>Política de devolucions</p>
        <p>Política de cookies</p>
      </div>
      <div class="footer-section">
        <p>Telèfon de contacte: <br> 623456977</p>
        <p>Segueix-nos:</p>
      </div>
      <div class="footer-section">
        <p>correu@perlart.com</p>
      </div>
    </div>
  </footer>
<script src="./validacion.js"></script>
</body>
</html>
