<?php
header('Content-Type: application/json');
require '../../middleware/auth.php';
require '../../db.php'; // jouw DB connectie ($pdo)

// Input ophalen
$vertoningId = $_GET['vertoning_id'] ?? null;
$ticketType  = $_GET['ticket_type'] ?? null; // normaal | kind | senior
$aantal      = $_GET['aantal'] ?? 1;

if (!$vertoningId || !$ticketType) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing vertoning_id or ticket_type']);
    exit;
}

// ---- 1. Haal vertoning + joins op ----
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
    WHERE v.id = ?
");
$stmt->execute([$vertoningId]);
$vertoning = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$vertoning) {
    http_response_code(404);
    echo json_encode(['error' => 'Vertoning not found']);
    exit;
}

// ---- 2. Kies juiste ticket prijs ----
$ticketLabel = null;
$prijs = null;

switch (strtolower($ticketType)) {
    case 'normaal':
        $ticketLabel = "Normaal";
        $prijs = $vertoning['prijs_normaal'];
        break;
    case 'kind':
        $ticketLabel = "Kind t/m 11 jaar";
        $prijs = $vertoning['prijs_kind'];
        break;
    case 'senior':
        $ticketLabel = "65+";
        $prijs = $vertoning['prijs_senior'];
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid ticket_type']);
        exit;
}

$totaal = $prijs * (int)$aantal;

// ---- 3. Afbeelding absolute URL maken ----
$imageBase = "https://image.tmdb.org/t/p/w500";
$poster = !empty($vertoning['poster_path']) 
    ? $imageBase . $vertoning['poster_path'] 
    : null;

// ---- 4. Response samenstellen ----
$response = [
    "film" => [
        "id"     => $vertoning['movie_id'],
        "title"  => $vertoning['title'],
        "poster" => $poster
    ],
    "bioscoop" => [
        "id"   => $vertoning['vestiging_id'],
        "naam" => $vertoning['vestiging_name'],
        "zaal" => $vertoning['zaal_name']
    ],
    "vertoning" => [
        "id"        => $vertoning['vertoning_id'],
        "starttijd" => $vertoning['starttijd']
    ],
    "ticket" => [
        "type"   => $ticketLabel,
        "prijs"  => (float)$prijs,
        "aantal" => (int)$aantal,
        "totaal" => (float)$totaal
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);