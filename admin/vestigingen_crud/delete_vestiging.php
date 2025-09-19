<?php
require '../database.sql/db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Eerst gekoppelde data verwijderen
    $pdo->prepare("DELETE FROM openingstijden WHERE vestiging_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM zalen WHERE vestiging_id = ?")->execute([$id]);

    // Daarna de vestiging zelf verwijderen
    $pdo->prepare("DELETE FROM vestigingen WHERE id = ?")->execute([$id]);
}

header("Location: list_vestigingen.php");
exit;
