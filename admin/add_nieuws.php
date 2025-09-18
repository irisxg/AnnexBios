<?php
session_start();

// Verbinding maken met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annexbios"; // Database voor bioscoop

$conn = new mysqli($servername, $username, $password, $dbname);

// Controleren op fouten bij verbinden
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleren of formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"];
    $publiceerdatum = $_POST["publiceerdatum"];
    $beschrijving = ($_POST["beschrijving"]);


    // Afbeelding uploaden en bestandsnaam opslaan
    $target_dir = "assets/img/";
    $afbeelding = basename($_FILES["afbeelding"]["name"]);
    move_uploaded_file($_FILES["afbeelding"]["tmp_name"], $target_dir . $afbeelding);

    // Gegevens opslaan in database
    $sql = "INSERT INTO nieuws (titel, publiceerdatum, beschrijving, afbeelding) 
            VALUES ('$titel', '$publiceerdatum', '$beschrijving', '$afbeelding')";

    if ($conn->query($sql) === TRUE) {
        // Redirect naar overzicht na succesvol toevoegen
        header("Location: nieuws.php");
        exit();
    } else {
        echo "Fout bij toevoegen: " . $conn->error;
    }
}

$conn->close();
?>
<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nieuwsbericht toevoegen</title>
    <link rel="stylesheet" href="assets/nieuws.css">
</head>

<body>
    <div id="content">
        <?php include "includes/header.php"; ?>

        <main>
            <form action="" method="post" enctype="multipart/form-data" class="formulier">
                <h2>Nieuwsbericht toevoegen</h2><br>

                <label for="afbeelding">Afbeelding:</label>
                <input type="file" name="afbeelding" id="afbeelding" required>

                <label for="titel">Titel:</label>
                <input type="text" name="titel" id="titel" required>

                <label for="publiceerdatum">Publiceerdatum:</label>
                <input type="date" name="publiceerdatum" id="publiceerdatum" required>

                <label for="beschrijving">Beschrijving:</label>
                <textarea name="beschrijving" id="beschrijving" required wrap="soft" maxlength="500"></textarea>

                <input type="submit" value="Nieuwsbericht toevoegen">

                <a href="nieuws.php" class="terug-knop">Terug naar overzicht</a>
            </form>
        </main>

        <?php include "includes/footer.php"; ?>
    </div>
</body>

</html>