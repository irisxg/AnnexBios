<?php
// import.php
require 'db.php';
require 'MovieImportService.php';

$apiKey = "eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiIxNGIxNjZhNTVlMjAxYWM0MzgxOWM5NTJlM2M2YWQ4MyIsIm5iZiI6MTc1NzUyNTM5Mi4wODMwMDAyLCJzdWIiOiI2OGMxYjU5MDQ0Y2RlMGU1ODQxZjYzMzMiLCJzY29wZXMiOlsiYXBpX3JlYWQiXSwidmVyc2lvbiI6MX0.s6tEPRBMuT8H2Urtj_Z6t4xWaUryBU77ODCuRB9Z6wM";

$service = new MovieImportService($pdo, $apiKey);

// Import voorbeeldfilms (TMDb IDs)
$service->importMovies([550, 603, 1007734]);
