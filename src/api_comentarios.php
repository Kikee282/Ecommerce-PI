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
    if (empty($input['text'])) { http_response_code(400); exit; }

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
    if (!$userId) { http_response_code(401); exit; }

    $idComentari = $_GET['id'] ?? null;
    if (!$idComentari) exit;

    $comentari = json_decode(@file_get_contents("$baseUrl/$idComentari"), true);
    if (!$comentari) { http_response_code(404); exit; }

    // PERMISOS: Esborro si és meu O sóc Admin
    if ((string)$comentari['userId'] === (string)$userId || $userRole === 'admin') {
        enviarPeticio("$baseUrl/$idComentari", 'DELETE');
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'No tens permís']);
    }
    exit;
}

// --- 4. EDITAR (PUT) ---
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    if (!$userId) { http_response_code(401); exit; }

    $input = json_decode(file_get_contents('php://input'), true);
    $idComentari = $input['id'] ?? null;
    
    $comentariOriginal = json_decode(@file_get_contents("$baseUrl/$idComentari"), true);
    if (!$comentariOriginal) { http_response_code(404); exit; }

    // CAMBIO CLAVE AQUÍ:
    // Antes solo permitía al dueño. Ahora añadimos "|| $userRole === 'admin'"
    if ((string)$comentariOriginal['userId'] === (string)$userId || $userRole === 'admin') {
        
        $comentariOriginal['text'] = htmlspecialchars($input['text']);
        $comentariOriginal['data'] = date('c'); // Actualitzem data d'edició

        enviarPeticio("$baseUrl/$idComentari", 'PUT', $comentariOriginal);
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Només pots editar els teus comentaris o ser administrador']);
    }
    exit;
}

// FUNCIÓ AUXILIAR
function enviarPeticio($url, $method, $data = null) {
    $opts = [
        'http' => [
            'method'  => $method,
            'header'  => "Content-type: application/json\r\n",
            'ignore_errors' => true
        ]
    ];
    if ($data) {
        $opts['http']['content'] = json_encode($data);
    }
    
    $context  = stream_context_create($opts);
    $result = @file_get_contents($url, false, $context);

    if (isset($http_response_header[0])) {
        preg_match('#HTTP/\S+\s+(\d{3})#', $http_response_header[0], $matches);
        if(isset($matches[1])) http_response_code($matches[1]);
    }

    echo $result ? $result : json_encode(['status' => 'error']);
}
?>