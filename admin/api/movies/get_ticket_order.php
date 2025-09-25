<?php
header('Content-Type: application/json');
require '../../middleware/auth.php';

// ✅ Input ophalen
$movieId = $_GET['movie_id'] ?? null;

if (!$movieId) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'status'  => 400,
        'message' => 'Missing movie_id'
    ]);
    exit;
}

// ✅ Vestiging ID komt uit auth.php (gekoppeld aan API key)
if (!isset($vestiging_id)) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'status'  => 401,
        'message' => 'Unauthorized: vestiging_id missing from middleware'
    ]);
    exit;
}

try {
    // ---- 1. Haal alle vertoningen voor deze film & vestiging ----
    $stmt = $pdo->prepare("
        SELECT v.id AS vertoning_id, v.starttijd,
               v.prijs_normaal, v.prijs_kind, v.prijs_senior,
               m.id AS movie_id, m.title, m.poster_path,
               z.name AS zaal_name,
               ve.id AS vestiging_id, ve.name AS vestiging_name
        FROM vertoningen v
        INNER JOIN movies m ON v.movie_id = m.id
        INNER JOIN zalen z ON v.zaal_id = z.id
        INNER JOIN vestigingen ve ON v.vestiging_id = ve.id
        WHERE v.movie_id = ? AND v.vestiging_id = ?
        ORDER BY v.starttijd ASC
    ");
    $stmt->execute([$movieId, $vestiging_id]);
    $vertoningen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$vertoningen) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'status'  => 404,
            'message' => 'No screenings found for this movie in this vestiging'
        ]);
        exit;
    }

    // ---- 2. Bouw response per vertoning ----
    $imageBase = "https://image.tmdb.org/t/p/w500";
    $data = [];

    foreach ($vertoningen as $v) {
        $poster = !empty($v['poster_path'])
            ? $imageBase . $v['poster_path']
            : null;

        $data[] = [
            "film" => [
                "id"     => $v['movie_id'],
                "title"  => $v['title'],
                "poster" => $poster
            ],
            "bioscoop" => [
                "id"   => $v['vestiging_id'],
                "naam" => $v['vestiging_name'],
                "zaal" => $v['zaal_name']
            ],
            "vertoning" => [
                "id"        => $v['vertoning_id'],
                "starttijd" => $v['starttijd']
            ],
            "prijzen" => [
                "normaal" => (float)$v['prijs_normaal'],
                "kind"    => (float)$v['prijs_kind'],
                "senior"  => (float)$v['prijs_senior']
            ]
        ];
    }

    // ---- 3. Stuur success response ----
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'status'  => 200,
        'message' => 'OK',
        'data'    => $data
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'status'  => 500,
        'message' => 'Internal Server Error',
        'error'   => $e->getMessage()
    ]);
}