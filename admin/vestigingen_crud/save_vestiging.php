<?php
require '../database.sql/db.php';

$id = $_POST['id'] ?? null;
$name = $_POST['name'];
$apiKey = $_POST['api_key'];
$openingtijden = $_POST['openingstijden'] ?? [];
$zalen = $_POST['zalen'] ?? [];

// Bij update
if ($id) {
    if (!empty($_POST['api_key'])) {
        $stmt = $pdo->prepare("UPDATE vestigingen SET name = ?, api_key = ? WHERE id = ?");
        $stmt->execute([$name, $apiKey, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE vestigingen SET name = ? WHERE id = ?");
        $stmt->execute([$name, $id]);
    }
} else {
    // Nieuwe vestiging
    $apiKey = !empty($_POST['api_key']) ? $_POST['api_key'] : bin2hex(random_bytes(8));
    $stmt = $pdo->prepare("INSERT INTO vestigingen (name, api_key) VALUES (?, ?)");
    $stmt->execute([$name, $apiKey]);
    $id = $pdo->lastInsertId();
}

// Oude data weggooien
$pdo->prepare("DELETE FROM openingstijden WHERE vestiging_id = ?")->execute([$id]);
$pdo->prepare("DELETE FROM zalen WHERE vestiging_id = ?")->execute([$id]);

// Nieuwe openingstijden invoegen
foreach ($openingtijden as $ot) {
    // Alleen overslaan als de waarde NIET bestaat of leeg string is
    if (!isset($ot['day_of_week']) || $ot['day_of_week'] === '') continue;

    $stmt = $pdo->prepare("
        INSERT INTO openingstijden (vestiging_id, day_of_week, open_time, close_time)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $id,
        $ot['day_of_week'],
        $ot['open_time'] !== '' ? $ot['open_time'] : null,
        $ot['close_time'] !== '' ? $ot['close_time'] : null
    ]);
}

// Nieuwe zalen invoegen
foreach ($zalen as $zaal) {
    // Alleen overslaan als er geen naam is
    if (!isset($zaal['name']) || $zaal['name'] === '') continue;

    $stmt = $pdo->prepare("
        INSERT INTO zalen (vestiging_id, name, capacity, type)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([
        $id,
        $zaal['name'],
        isset($zaal['capacity']) && $zaal['capacity'] !== '' ? $zaal['capacity'] : 0,
        $zaal['type'] ?? null
    ]);
}

header("Location: list_vestigingen.php");
exit;
