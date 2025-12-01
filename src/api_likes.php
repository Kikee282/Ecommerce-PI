<?php
session_start();
header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
$productId = $_GET['productId'] ?? null;

// --- LEER LIKES (GET) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$productId) exit(json_encode(['count' => 0, 'liked' => false]));

    // 1. Pedir todos los likes de este producto al JSON Server
    $url = "http://jsonserver:3000/likes?productId=" . $productId;
    $likes = json_decode(file_get_contents($url), true);

    // 2. Contar
    $count = count($likes);

    // 3. Ver si YO le he dado like
    $userLiked = false;
    if ($userId) {
        foreach ($likes as $like) {
            // Comparamos IDs (cuidado con strings/numeros)
            if ((string)$like['userId'] === (string)$userId) {
                $userLiked = true;
                break;
            }
        }
    }

    echo json_encode(['count' => $count, 'liked' => $userLiked]);
    exit;
}

// --- PONER/QUITAR LIKE (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$userId) {
        http_response_code(401);
        echo json_encode(['error' => 'No logueado']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $prodId = $input['productId'];

    // 1. Comprobar si ya existe el like
    $checkUrl = "http://jsonserver:3000/likes?productId=$prodId&userId=$userId";
    $existing = json_decode(file_get_contents($checkUrl), true);

    if (count($existing) > 0) {
        // YA EXISTE -> BORRAR (Dislike)
        $likeId = $existing[0]['id'];
        $ch = curl_init("http://jsonserver:3000/likes/$likeId");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_exec($ch);
        curl_close($ch);
        echo json_encode(['status' => 'removed']);
    } else {
        // NO EXISTE -> CREAR (Like)
        $data = ['productId' => $prodId, 'userId' => $userId];
        $ch = curl_init("http://jsonserver:3000/likes");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_exec($ch);
        curl_close($ch);
        echo json_encode(['status' => 'added']);
    }
    exit;
}
?>