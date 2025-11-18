<?php
session_start();

// 1. SEGURETAT: Si no hi ha sessió, fora.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

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
        // Nota: No deixem canviar 'nom_usuari' ni 'contrasenya' aquí per seguretat bàsica
    ];

    // Preparem la petició PATCH
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH"); // Mètode PATCH
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $missatge = "<p style='color: green;'>Dades actualitzades correctament!</p>";
        // Actualitzem també la sessió per si ha canviat el nom visible
        $_SESSION['user_real_name'] = $updateData['nom'];
    } else {
        $missatge = "<p style='color: red;'>Error al guardar els canvis.</p>";
    }
}

// 3. OBTENIR DADES ACTUALS (CONSULTA - GET)
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$jsonResponse = curl_exec($ch);
curl_close($ch);

$userData = json_decode($jsonResponse, true);

// Si l'usuari no es troba al JSON Server (per error de sincronització)
if (!$userData) {
    die("Error: No s'han pogut carregar les dades del perfil.");
}
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>El meu Perfil</title>
    <link rel="stylesheet" href="../styles/styleIndex.css">
    <style>
        /* Estils específics per al perfil */
        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .readonly { background-color: #e9ecef; cursor: not-allowed; }
        .btn-back { background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 20px; margin-right: 10px; }
    </style>
</head>
<body>
    
    <header>
        <nav class="navbar">
            <ul>
                <div class="logoHeader">
                     <img src="../contenido/logoParteArriba.png" alt="Logo">
                </div>
                <li><a href="../index.php">Tornar a l'Inici</a></li>
                <li><a href="./logout.php" style="color: red;">Tancar Sessió</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <h2>El meu Perfil</h2>
            
            <?php echo $missatge; ?>

            <form method="POST" action="profile.php">
                
                <div class="form-group">
                    <label>Nom d'usuari (No es pot canviar):</label>
                    <input type="text" value="<?php echo htmlspecialchars($userData['nom_usuari']); ?>" class="readonly" readonly>
                </div>

                <div class="form-group">
                    <label>Data de registre:</label>
                    <input type="text" value="<?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($userData['data_registre']))); ?>" class="readonly" readonly>
                </div>

                <hr>

                <div class="form-group">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($userData['nom'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="cognoms">Cognoms:</label>
                    <input type="text" id="cognoms" name="cognoms" value="<?php echo htmlspecialchars($userData['cognoms'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Correu Electrònic:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" required>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <a href="index.php" class="btn-back">Cancel·lar</a>
                    <button type="submit" class="btn">Guardar Canvis</button>
                </div>

            </form>
        </div>
    </main>
</body>
</html>