<?php
header('Content-Type: application/json');
require '../../middleware/auth.php';

$movieId = $_GET['movie_id'] ?? null;

if (!$movieId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing movie_id']);
    exit;
}

$imageBase = "https://image.tmdb.org/t/p/w500";

// 1. Basis filmgegevens
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movieId]);
$movie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$movie) {
    http_response_code(404);
    echo json_encode(['error' => 'Movie not found']);
    exit;
}

// Absolute paden en sterren berekenen
if (!empty($movie['poster_path'])) {
    $movie['poster_path'] = $imageBase . $movie['poster_path'];
}
if (!empty($movie['backdrop_path'])) {
    $movie['backdrop_path'] = $imageBase . $movie['backdrop_path'];
}
$movie['stars'] = isset($movie['vote_average'])
    ? round(($movie['vote_average'] / 10) * 5, 1)
    : null;

// 2. Genres
$stmt = $pdo->prepare("
    SELECT g.id, g.name
    FROM genres g
    INNER JOIN movie_genres mg ON g.id = mg.genre_id
    WHERE mg.movie_id = ?
");
$stmt->execute([$movieId]);
$genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Cast
$stmt = $pdo->prepare("
    SELECT a.id, a.name, a.profile_path, mc.character, mc.cast_order
    FROM actors a
    INNER JOIN movie_cast mc ON a.id = mc.actor_id
    WHERE mc.movie_id = ?
    ORDER BY mc.cast_order ASC
");
$stmt->execute([$movieId]);
$cast = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cast as &$c) {
    if (!empty($c['profile_path'])) {
        $c['profile_path'] = $imageBase . $c['profile_path'];
    }
}

// 4. Crew
$stmt = $pdo->prepare("
    SELECT person_id, name, job, department
    FROM crew
    WHERE movie_id = ?
");
$stmt->execute([$movieId]);
$crew = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Videos
$stmt = $pdo->prepare("
    SELECT site, `key`, type, official
    FROM videos
    WHERE movie_id = ?
");
$stmt->execute([$movieId]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 6. Vertoningen per vestiging
$stmt = $pdo->prepare("
    SELECT v.id, v.starttijd, v.prijs,
           z.id AS zaal_id, z.name AS zaal_name, z.capacity
    FROM vertoningen v
    INNER JOIN zalen z ON v.zaal_id = z.id
    WHERE v.movie_id = ? AND v.vestiging_id = ?
    ORDER BY v.starttijd ASC
");
$stmt->execute([$movieId, $vestiging_id]);
$vertoningen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 7. Response
$response = [
    'movie' => $movie,
    'genres' => $genres,
    'cast' => $cast,
    'crew' => $crew,
    'videos' => $videos,
    'vertoningen' => $vertoningen
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);