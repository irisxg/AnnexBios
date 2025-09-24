<?php

require __DIR__ . '/../database.sql/db.php';


$headers = function_exists('getallheaders') ? getallheaders() : [];
if (!$headers) {
    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $name = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
            $headers[$name] = $value;
        }
    }
}

// Zoek naar api-key header (case-insensitive)
$apiKey = $headers['api-key'] ?? $headers['Api-Key'] ?? $headers['API-KEY'] ?? null;

if (!$apiKey) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized: No api-key provided']);
    exit;
}

// Check of api-key geldig is
$stmt = $pdo->prepare("SELECT * FROM vestigingen WHERE api_key = ?");
$stmt->execute([$apiKey]);
$vestiging = $stmt->fetch();

if (!$vestiging) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Invalid api-key']);
    exit;
}

// Beschikbaar in endpoints
$vestiging_id = $vestiging['id'];
