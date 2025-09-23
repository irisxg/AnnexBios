<?php
require '../database.sql/db.php';

$vestiging_id = $_GET['vestiging_id'] ?? 0;

$stmt = $pdo->prepare("SELECT id, name, capacity FROM zalen WHERE vestiging_id = ?");
$stmt->execute([$vestiging_id]);
$zalen = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($zalen);
