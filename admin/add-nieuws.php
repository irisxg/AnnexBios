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
    $beschrijving = $_POST["beschrijving"];

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
    <style>
        .formulier {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        label {
            display: block;
            margin-top: 1em;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 0.5em;
            box-sizing: border-box;
        }

        input[type="file"] {
            margin-top: 0.5em;
        }

        input[type="submit"] {
            margin-top: 1.5em;
            padding: 0.6em 1.2em;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .terug-knop {
            display: block;
            margin-top: 1em;
            color: #000;
            text-decoration: none;
        }
    </style>
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
                <textarea name="beschrijving" id="beschrijving" required></textarea>

                <input type="submit" value="Nieuwsbericht toevoegen">

                <a href="nieuws.php" class="terug-knop">Terug naar overzicht</a>
            </form>
        </main>

        <?php include "includes/footer.php"; ?>
    </div>
</body>
</html>
