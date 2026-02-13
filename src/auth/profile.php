<?php
session_start();

// 1. SEGURETAT: Si no hi ha sessió, fora.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Variables para el Nav
$isLoggedIn = true; 
$nombreUsuario = $_SESSION['user_real_name'];
$userId = $_SESSION['user_id'];
$apiUrl = "http://jsonserver:3000/usuaris/" . $userId;
$missatge = "";

// 2. PROCESSAR EL FORMULARI (ACTUALITZAR DADES - PATCH)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recollim les dades del formulari
    $updateData = [
        "nom" => trim($_POST['nom']),
        "cognoms" => trim($_POST['cognoms']),
        "email" => trim($_POST['email'])
    ];

    // Preparem la petició PATCH
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $missatge = "<p class='alert alert-success'>Dades actualitzades correctament!</p>";
        $_SESSION['user_real_name'] = $updateData['nom'];
        $nombreUsuario = $updateData['nom']; // Actualizar variable local para el nav
    } else {
        $missatge = "<p class='alert alert-danger'>Error al guardar els canvis.</p>";
    }
}

// 3. OBTENIR DADES ACTUALS (CONSULTA - GET)
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$jsonResponse = curl_exec($ch);
curl_close($ch);

$userData = json_decode($jsonResponse, true);

if (!$userData) {
    die("Error: No s'han pogut carregar les dades del perfil.");
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El meu Perfil</title>
    
    <link href="../styles/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="../styles/common.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../styles/styleIndex.css">
    
    <style>
        .profile-container {
            max-width: 600px;
            margin: 100px auto 40px; /* Margen superior para evitar solapamiento con nav fijo */
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 15px; }
        .readonly { background-color: #e9ecef; cursor: not-allowed; }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm py-3">
    <div class="container-fluid px-4">
        
        <a class="navbar-brand" href="../index.php">
            <img src="../contenido/logoParteArriba.png" alt="Logo" style="height: 50px;">
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center align-items-center">
                <li class="nav-item"><a class="nav-link" href="../productos.php">Productes</a></li>
                <li class="nav-item"><a class="nav-link" href="../sobre_nosaltres.php">Sobre nosaltres</a></li>
                <li class="nav-item"><a class="nav-link" href="../contacte.php">Contacte</a></li>

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
                        <a class="nav-link text-danger" href="logout.php">
                            Tancar Sessió
                        </a>
                    </li>

                <?php endif; ?>

                <li class="nav-item d-flex align-items-center justify-content-center gap-3 mt-2 mt-lg-0">
                    
                    <?php if ($isLoggedIn): ?>
                        <a class="nav-link p-0" href="profile.php">
                            <i class="fas fa-user fs-5"></i>
                        </a>
                    <?php else: ?>
                        <a class="nav-link p-0" href="login.html">
                            <i class="fas fa-user fs-5"></i>
                        </a>
                    <?php endif; ?>

                    <a class="nav-link p-0" href="#">
                        <a class="nav-link p-0 position-relative" href="../carret.php">
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
        <div class="profile-container">
            <h2 class="mb-4">El meu Perfil</h2>
            
            <?php echo $missatge; ?>

            <form method="POST" action="">
                
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Nom d'usuari:</label>
                    <input type="text" value="<?php echo htmlspecialchars($userData['nom_usuari']); ?>" class="form-control readonly" readonly>
                </div>

                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Data de registre:</label>
                    <input type="text" value="<?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($userData['data_registre']))); ?>" class="form-control readonly" readonly>
                </div>

                <hr class="my-4">

                <div class="form-group mb-3">
                    <label for="nom" class="form-label fw-bold">Nom:</label>
                    <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($userData['nom'] ?? ''); ?>" required>
                </div>

                <div class="form-group mb-3">
                    <label for="cognoms" class="form-label fw-bold">Cognoms:</label>
                    <input type="text" id="cognoms" name="cognoms" class="form-control" value="<?php echo htmlspecialchars($userData['cognoms'] ?? ''); ?>">
                </div>

                <div class="form-group mb-4">
                    <label for="email" class="form-label fw-bold">Correu Electrònic:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <a href="../index.php" class="btn btn-secondary rounded-pill px-4">Cancel·lar</a>
                    <button type="submit" class="btn btn-dark rounded-pill px-4">Guardar Canvis</button>
                </div>

            </form>
        </div>
    </main>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/logicCarret.js"></script>
    
    <script>
        function setFontSize(size) {
            document.body.classList.remove('font-small', 'font-normal', 'font-large', 'font-xlarge');
            document.body.classList.add('font-' + size);
        }
    </script>
</body>
</html>