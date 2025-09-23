<?php
header('Content-Type: application/json');
require '../../middleware/auth.php';

$imageBase = "https://image.tmdb.org/t/p/w500";

$day         = $_GET['day'] ?? null;
$genreId     = $_GET['genre_id'] ?? null;
$actorId     = $_GET['actor_id'] ?? null;
$directorId  = $_GET['director_id'] ?? null;
$minScore    = $_GET['min_score'] ?? null;
$maxDuration = $_GET['max_duration'] ?? null;
$hasTrailer  = $_GET['has_trailer'] ?? null;

$sql = "
    SELECT DISTINCT m.id, m.title, m.release_date, m.vote_average, 
           m.overview, m.runtime, m.poster_path
    FROM movies m
    LEFT JOIN movie_genres mg ON m.id = mg.movie_id
    LEFT JOIN genres g ON mg.genre_id = g.id
    LEFT JOIN movie_cast mc ON m.id = mc.movie_id
    LEFT JOIN actors a ON mc.actor_id = a.id
    LEFT JOIN crew c ON m.id = c.movie_id
    LEFT JOIN videos v ON m.id = v.movie_id
    WHERE 1=1
";

$params = [];

if ($day) {
    $sql .= " AND DATE(m.release_date) = ? ";
    $params[] = $day;
}
if ($genreId) {
    $sql .= " AND g.id = ? ";
    $params[] = $genreId;
}
if ($actorId) {
    $sql .= " AND a.id = ? ";
    $params[] = $actorId;
}
if ($directorId) {
    $sql .= " AND c.person_id = ? AND c.job = 'Director' ";
    $params[] = $directorId;
}
if ($minScore) {
    $sql .= " AND m.vote_average >= ? ";
    $params[] = ($minScore / 5) * 10;
}
if ($maxDuration) {
    $sql .= " AND m.runtime <= ? ";
    $params[] = $maxDuration;
}
if ($hasTrailer !== null) {
    if ($hasTrailer == 1) {
        $sql .= " AND EXISTS (
            SELECT 1 FROM videos vv WHERE vv.movie_id = m.id AND vv.type = 'Trailer'
        )";
    } else {
        $sql .= " AND NOT EXISTS (
            SELECT 1 FROM videos vv WHERE vv.movie_id = m.id AND vv.type = 'Trailer'
        )";
    }
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

$response = [];

foreach ($movies as $movie) {
    if (!empty($movie['poster_path'])) {
        $movie['poster'] = $imageBase . $movie['poster_path'];
    } else {
        $movie['poster'] = null;
    }

    $movie['stars'] = isset($movie['vote_average'])
        ? round(($movie['vote_average'] / 10) * 5, 1)
        : null;

    $stmt = $pdo->prepare("
        SELECT g.id, g.name 
        FROM genres g 
        INNER JOIN movie_genres mg ON g.id = mg.genre_id 
        WHERE mg.movie_id = ?
    ");
    $stmt->execute([$movie['id']]);
    $movie['genres'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT person_id, name 
        FROM crew 
        WHERE movie_id = ? AND job = 'Director'
        LIMIT 1
    ");
    $stmt->execute([$movie['id']]);
    $movie['director'] = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM videos 
        WHERE movie_id = ? AND type = 'Trailer'
    ");
    $stmt->execute([$movie['id']]);
    $movie['has_trailer'] = $stmt->fetchColumn() > 0;

    unset($movie['poster_path'], $movie['vote_average']);

    $response[] = $movie;
}

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
