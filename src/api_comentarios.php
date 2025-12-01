<?php
// src/api_comentarios.php
session_start();
header('Content-Type: application/json');

// --- CASO 1: LECTURA DE COMENTARIOS (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    // Recogemos el ID del producto de la URL
    $productId = $_GET['productId'] ?? null;
    
    if (!$productId) {
        echo json_encode([]); // Si no hay ID, devolvemos lista vacía
        exit;
    }

    // El servidor PHP (Docker) llama al JSON Server internamente (http)
    // Aquí sí está permitido porque no pasa por el navegador del cliente
    $apiUrl = "http://jsonserver:3000/comentaris?productId=" . $productId . "&_sort=data&_order=desc";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    echo $response ? $response : json_encode([]);
    exit;
}

// --- CASO 2: NUEVO COMENTARIO (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Has d\'iniciar sessió per comentar']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (empty($input['text']) || empty($input['productId'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Falten dades']);
        exit;
    }

    $nuevoComentario = [
        'productId' => (int)$input['productId'],
        'userId'    => $_SESSION['user_id'],
        'nom_usuari'=> $_SESSION['user_real_name'] ?? 'Usuari',
        'text'      => htmlspecialchars($input['text']),
        'puntuacio' => (int)($input['puntuacio'] ?? 5),
        'data'      => date('c')
    ];

    $ch = curl_init('http://jsonserver:3000/comentaris');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($nuevoComentario));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    http_response_code($httpCode);
    echo $result;
    exit;
}

// Si no es GET ni POST
http_response_code(405);
echo json_encode(['error' => 'Mètode no permès']);
?>