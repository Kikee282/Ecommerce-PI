<?php
header('Content-Type: application/json');

// Recibir datos del fetch (JS)
$input = json_decode(file_get_contents('php://input'), true);
$method = $_SERVER['REQUEST_METHOD'];

// URL base del JSON Server (interno de Docker)
$baseUrl = "http://jsonserver:3000/likes";

if ($method === 'POST') {
    // 1. OBTENER DATOS
    $userId = $input['user_id'] ?? null;
    $prodId = $input['product_id'] ?? null;

    if (!$userId || !$prodId) {
        echo json_encode(['success' => false, 'message' => 'Falten dades']);
        exit;
    }

    // 2. BUSCAR SI YA EXISTE EL LIKE
    // Filtramos por usuario y producto
    $queryUrl = "$baseUrl?user_id=$userId&product_id=$prodId";
    $json = file_get_contents($queryUrl);
    $existingLikes = json_decode($json, true);

    if (count($existingLikes) > 0) {
        // --- YA EXISTE -> ELIMINAR (UNLIKE) ---
        $likeId = $existingLikes[0]['id'];
        
        $ch = curl_init("$baseUrl/$likeId");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        
        $liked = false;
    } else {
        // --- NO EXISTE -> CREAR (LIKE) ---
        $data = json_encode([
            "user_id" => $userId,
            "product_id" => $prodId,
            "timestamp" => time()
        ]);

        $ch = curl_init($baseUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
        
        $liked = true;
    }

    // 3. RECONTAR TOTAL DE LIKES DEL PRODUCTO
    $countJson = file_get_contents("$baseUrl?product_id=$prodId");
    $totalLikes = count(json_decode($countJson, true));

    echo json_encode([
        'success' => true,
        'liked' => $liked,
        'count' => $totalLikes
    ]);
    exit;
}

// SI ES UN GET (Para comprobar estado inicial)
if ($method === 'GET') {
    $userId = $_GET['user_id'] ?? null;
    $prodId = $_GET['product_id'] ?? null;

    // Contar totales
    $countJson = file_get_contents("$baseUrl?product_id=$prodId");
    $totalLikes = count(json_decode($countJson, true));

    // Ver si el usuario actual le dio like
    $isLiked = false;
    if ($userId) {
        $checkJson = file_get_contents("$baseUrl?user_id=$userId&product_id=$prodId");
        $checks = json_decode($checkJson, true);
        if (count($checks) > 0) $isLiked = true;
    }

    echo json_encode([
        'liked' => $isLiked,
        'count' => $totalLikes
    ]);
    exit;
}
?>