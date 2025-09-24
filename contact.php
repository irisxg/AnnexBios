<?php
session_start();
require '../database.sql/db.php';
include "../includes/header.php";

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $naam = trim($_POST['naam']);
    $email = trim($_POST['email']);
    $bericht = trim($_POST['bericht']);

    if (empty($naam) || empty($email) || empty($bericht)) {
        $error = "Vul alle velden in.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ongeldig e-mailadres.";
    } else {
        $sql = "INSERT INTO contact_messages (naam, email, bericht, datum) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $naam, $email, $bericht);

        if ($stmt->execute()) {
            $success = "Bericht succesvol verzonden!";
        } else {
            $error = "Er is iets misgegaan: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Admin</title>
    <link rel="stylesheet" href="assets/contact.css">
</head>
<body>
    <div id="content">
        <h2>Contact Admin</h2>

        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="post" class="formulier">
            <div class="form-group">
                <label for="naam">Naam:</label>
                <input type="text" id="naam" name="naam" required>
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="bericht">Bericht:</label>
                <textarea id="bericht" name="bericht" rows="5" required></textarea>
            </div>

            <div class="form-group">
                <input type="submit" value="Verstuur">
            </div>
        </form>
    </div>
</body>
</html>
