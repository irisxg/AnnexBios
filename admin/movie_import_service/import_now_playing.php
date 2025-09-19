<?php
require '../database.sql/db.php';
require 'MovieImportService.php';

$apiKey = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxNGIxNjZhNTVlMjAxYWM0MzgxOWM5NTJlM2M2YWQ4MyIsIm5iZiI6MTc1NzUyNTM5Mi4wODMwMDAyLCJzdWIiOiI2OGMxYjU5MDQ0Y2RlMGU1ODQxZjYzMzMiLCJzY29wZXMiOlsiYXBpX3JlYWQiXSwidmVyc2lvbiI6MX0.s6tEPRBMuT8H2Urtj_Z6t4xWaUryBU77ODCuRB9Z6wM";
$service = new MovieImportService($pdo, $apiKey);

$ids = [];
for ($page = 1; $page <= 4; $page++) {
    $url = "https://api.themoviedb.org/3/movie/now_playing?language=nl-NL&page={$page}";

    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "Authorization: Bearer {$apiKey}\r\n" .
                        "Accept: application/json\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    $json = file_get_contents($url, false, $context);

    if ($json === false) {
        die("❌ Fout bij ophalen van now_playing (pagina {$page})");
    }

    $data = json_decode($json, true);

    if (!empty($data['results'])) {
        foreach ($data['results'] as $movie) {
            $ids[] = $movie['id'];
        }
    }
}

echo "Gevonden " . count($ids) . " films uit now_playing.\n";

// Import de films met details
$service->importMovies($ids);