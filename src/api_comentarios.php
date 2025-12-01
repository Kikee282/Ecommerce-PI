<?php
// src/api_comentarios.php
session_start();
header('Content-Type: application/json');

$baseUrl = "http://jsonserver:3000/comentaris";
$userId = $_SESSION['user_id'] ?? null;
$userRole = $_SESSION['user_role'] ?? 'user';

// --- 1. LLEGIR (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $productId = $_GET['productId'] ?? null;
    if (!$productId) exit(json_encode([]));
    
    echo @file_get_contents($baseUrl . "?productId=" . $productId . "&_sort=data&_order=desc") ?: json_encode([]);
    exit;
}

// --- 2. CREAR (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$userId) { http_response_code(401); exit(json_encode(['error' => 'No loguejat'])); }
    
    $input = json_decode(file_get_contents('php://input'), true);
    // ... (Validacions igual que abans) ...
    if (empty($input['text'])) exit;

    $nuevo = [
        'productId' => (int)$input['productId'],
        'userId' => $userId,
        'nom_usuari' => $_SESSION['user_real_name'] ?? 'Usuari',
        'text' => htmlspecialchars($input['text']),
        'puntuacio' => (int)($input['puntuacio'] ?? 5),
        'data' => date('c')
    ];

    enviarPeticio($baseUrl, 'POST', $nuevo);
    exit;
}

// --- 3. ESBORRAR (DELETE) ---
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $userId = $_SESSION['user_id'] ?? null;
    $userRole = $_SESSION['user_role'] ?? 'user'; // Leemos el rol de la sesión

    if (!$userId) { http_response_code(401); exit; }

    $idComentari = $_GET['id'] ?? null;
    
    // Obtenemos el comentario para ver de quién es
    $comentari = json_decode(@file_get_contents("http://jsonserver:3000/comentaris/$idComentari"), true);
    
    if (!$comentari) { http_response_code(404); exit; }

    // PERMISOS: ¿Es mío O soy Admin?
    if ((string)$comentari['userId'] === (string)$userId || $userRole === 'admin') {
        
        // Enviamos DELETE al JSON Server
        $ch = curl_init("http://jsonserver:3000/comentaris/$idComentari");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($ch);
        curl_close($ch);
        echo json_encode(['status' => 'deleted']);
        
    } else {
        http_response_code(403); // Prohibido
        echo json_encode(['error' => 'No tens permís']);
    }
    exit;
}

// --- 4. EDITAR (PUT) ---
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $userId = $_SESSION['user_id'] ?? null;
    $input = json_decode(file_get_contents('php://input'), true);
    $idComentari = $input['id'] ?? null;

    // Obtenemos original
    $comentariOriginal = json_decode(@file_get_contents("http://jsonserver:3000/comentaris/$idComentari"), true);

    // Solo el dueño puede editar texto
    if ((string)$comentariOriginal['userId'] === (string)$userId) {
        // Actualizamos campos
        $comentariOriginal['text'] = htmlspecialchars($input['text']);
        $comentariOriginal['data'] = date('c');

        // Enviamos PUT
        $ch = curl_init("http://jsonserver:3000/comentaris/$idComentari");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($comentariOriginal));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
        echo json_encode(['status' => 'updated']);
    } else {
        http_response_code(403);
    }
    exit;
}

// Funció auxiliar per no repetir codi de stream_context
function enviarPeticio($url, $method, $data = null) {
    $opts = ['http' => ['method' => $method, 'header' => "Content-type: application/json\r\n"]];
    if ($data) $opts['http']['content'] = json_encode($data);
    
    echo @file_get_contents($url, false, stream_context_create($opts));
}
?>