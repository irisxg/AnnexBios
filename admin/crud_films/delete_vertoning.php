<?php
require '../database.sql/db.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM vertoningen WHERE id = ?");
$stmt->execute([$id]);

header("Location: list_vertoningen.php");
exit;