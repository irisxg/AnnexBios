<?php
// MovieImportService.php (v3) – centrale opslag van films, fixes toegepast

class MovieImportService
{
    private $pdo;
    private $apiKey;
    private $apiUrl = "https://api.themoviedb.org/3/movie/";

    public function __construct(PDO $pdo, string $apiKey)
    {
        $this->pdo = $pdo;
        $this->apiKey = $apiKey;
    }

    public function importMovies(array $movieIds): void
    {
        foreach ($movieIds as $id) {
            try {
                $this->importMovie($id);
                echo " Imported movie {$id}\n";
            } catch (Exception $e) {
                echo " Error importing movie {$id}: " . $e->getMessage() . "\n";
            }
        }
    }

    private function importMovie(int $id): void
    {
        $url = $this->apiUrl . $id . "?language=nl-NL&append_to_response=credits,videos";
        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Authorization: Bearer {$this->apiKey}\r\n" .
                    "Accept: application/json\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $json = file_get_contents($url, false, $context);
        if ($json === false) {
            throw new Exception("API call failed for movie {$id}");
        }

        $data = json_decode($json, true);
        if (!$data || !isset($data['id'])) {
            throw new Exception("Invalid response for movie {$id}");
        }

        try {
            $this->pdo->beginTransaction();

            $this->insertMovie($data);
            $this->insertGenres($data);
            $this->insertCast($data);
            $this->insertCrew($data);
            $this->insertVideos($data);

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack(); // rollback bij fout
            throw $e;
        }
    }

    private function insertMovie(array $data): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO `movies` (`id`, `title`, `overview`, `release_date`, `imdb_id`, `poster_path`, `backdrop_path`, `runtime`, `vote_average`, `origin_country`)
            VALUES (:id, :title, :overview, :release_date, :imdb_id, :poster_path, :backdrop_path, :runtime, :vote_average, :origin_country)
            ON DUPLICATE KEY UPDATE
              `title` = VALUES(`title`),
              `overview` = VALUES(`overview`),
              `release_date` = VALUES(`release_date`),
              `poster_path` = VALUES(`poster_path`),
              `backdrop_path` = VALUES(`backdrop_path`),
              `runtime` = VALUES(`runtime`),
              `vote_average` = VALUES(`vote_average`),
              `origin_country` = VALUES(`origin_country`);
        ");

        $stmt->execute([
            ':id' => $data['id'],
            ':title' => $data['title'],
            ':overview' => $data['overview'] ?? '',
            ':release_date' => $data['release_date'] ?? null,
            ':imdb_id' => $data['imdb_id'] ?? null,
            ':poster_path' => $data['poster_path'] ?? null,
            ':backdrop_path' => $data['backdrop_path'] ?? null,
            ':runtime' => $data['runtime'] ?? null,
            ':vote_average' => $data['vote_average'] ?? null,
            ':origin_country' => $data['origin_country'][0] ?? null,
        ]);
    }

    private function insertGenres(array $data): void
    {
        if (empty($data['genres'])) return;

        foreach ($data['genres'] as $genre) {
            $stmt = $this->pdo->prepare("
                INSERT INTO `genres` (`id`, `name`)
                VALUES (:id, :name)
                ON DUPLICATE KEY UPDATE `name` = VALUES(`name`)
            ");
            $stmt->execute([':id' => $genre['id'], ':name' => $genre['name']]);

            $stmt = $this->pdo->prepare("
                INSERT IGNORE INTO `movie_genres` (`movie_id`, `genre_id`)
                VALUES (:movie_id, :genre_id)
            ");
            $stmt->execute([':movie_id' => $data['id'], ':genre_id' => $genre['id']]);
        }
    }

    private function insertCast(array $data): void
    {
        if (empty($data['credits']['cast'])) return;

        foreach ($data['credits']['cast'] as $cast) {
            $stmt = $this->pdo->prepare("
                INSERT INTO `actors` (`id`, `name`, `profile_path`)
                VALUES (:id, :name, :profile_path)
                ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `profile_path` = VALUES(`profile_path`)
            ");
            $stmt->execute([
                ':id' => $cast['id'],
                ':name' => $cast['name'],
                ':profile_path' => $cast['profile_path'] ?? null,
            ]);

            $stmt = $this->pdo->prepare("
                INSERT INTO `movie_cast` (`movie_id`, `actor_id`, `character`, `cast_order`)
                VALUES (:movie_id, :actor_id, :character, :cast_order)
                ON DUPLICATE KEY UPDATE `character` = VALUES(`character`), `cast_order` = VALUES(`cast_order`)
            ");
            $stmt->execute([
                ':movie_id' => $data['id'],
                ':actor_id' => $cast['id'],
                ':character' => $cast['character'] ?? '',
                ':cast_order' => $cast['order'] ?? 0,
            ]);
        }
    }

    private function insertCrew(array $data): void
    {
        if (empty($data['credits']['crew'])) return;

        foreach ($data['credits']['crew'] as $crew) {
            $stmt = $this->pdo->prepare("
                INSERT INTO `crew` (`movie_id`, `person_id`, `name`, `job`, `department`)
                VALUES (:movie_id, :person_id, :name, :job, :department)
                ON DUPLICATE KEY UPDATE `job` = VALUES(`job`), `department` = VALUES(`department`)
            ");
            $stmt->execute([
                ':movie_id' => $data['id'],
                ':person_id' => $crew['id'],
                ':name' => $crew['name'],
                ':job' => $crew['job'] ?? null,
                ':department' => $crew['department'] ?? null,
            ]);
        }
    }

    private function insertVideos(array $data): void
    {
        if (empty($data['videos']['results'])) return;

        foreach ($data['videos']['results'] as $video) {
            $stmt = $this->pdo->prepare("
                INSERT INTO `videos` (`movie_id`, `site`, `key`, `type`, `official`)
                VALUES (:movie_id, :site, :key, :type, :official)
            ");
            $stmt->execute([
                ':movie_id' => $data['id'],
                ':site' => $video['site'] ?? null,
                ':key' => $video['key'] ?? null,
                ':type' => $video['type'] ?? null,
                ':official' => isset($video['official']) ? (int)$video['official'] : 0,
            ]);
        }
    }
}