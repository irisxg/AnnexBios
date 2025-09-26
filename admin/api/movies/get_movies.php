
<?php
header('Content-Type: application/json');
require '../../middleware/auth.php';

try {
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
    INNER JOIN vertoningen v ON m.id = v.movie_id
    WHERE v.vestiging_id = ?
";
$params = [$vestiging_id];



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

    if (!$movies) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'status' => 404,
            'message' => 'No movies found'
        ]);
        exit;
    }

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

    http_response_code(200);
    echo json_encode([
        'success' => true,
        'status' => 200,
        'message' => 'OK',
        'data' => $response
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'status' => 500,
        'message' => 'Internal Server Error',
        'error' => $e->getMessage()
    ]);
}
