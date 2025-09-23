<?php
session_start();
require '../database.sql/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"] ?? '';
    $publiceerdatum = $_POST["publiceerdatum"] ?? '';
    $beschrijving = $_POST["beschrijving"] ?? '';

    // Bestand uploaden
    $target_dir = "assets/img/";
    $origineleNaam = basename($_FILES["afbeelding"]["name"]);
    $bestandstype = $_FILES["afbeelding"]["type"];
    $toegestane_types = ["image/jpeg", "image/png", "image/gif"];

    if (!in_array($bestandstype, $toegestane_types)) {
        die("Ongeldig bestandstype. Alleen JPG, PNG en GIF zijn toegestaan.");
    }

    $uniekeNaam = uniqid() . "_" . $origineleNaam;
    $uploadPad = $target_dir . $uniekeNaam;

    if (!move_uploaded_file($_FILES["afbeelding"]["tmp_name"], $uploadPad)) {
        die("Uploaden van afbeelding mislukt.");
    }

    // Prepared statement gebruiken
    $stmt = $conn->prepare("INSERT INTO nieuws (titel, publiceerdatum, beschrijving, afbeelding) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $titel, $publiceerdatum, $beschrijving, $uniekeNaam);

    if ($stmt->execute()) {
        header("Location: nieuws.php");
        exit();
    } else {
        echo "Fout bij toevoegen: " . $stmt->error;
    }

    $stmt->close();
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
        <?php include "../includes/header.php"; ?>

        <main>
            <form action="" method="post" enctype="multipart/form-data" class="formulier">
                <h2>Nieuwsbericht toevoegen</h2><br>

                <label for="afbeelding">Afbeelding:</label>
                <input type="file" name="afbeelding" id="afbeelding" required>

                <label for="titel">Titel:</label>
                <input type="text" name="titel" id="titel" maxlength="255" required>

                <label for="publiceerdatum">Publiceerdatum:</label>
                <input type="date" name="publiceerdatum" id="publiceerdatum" required>

                <label for="beschrijving">Beschrijving:</label>
                <!-- Geen 'required' hier, TinyMCE valideert via JS -->
                <textarea name="beschrijving" id="beschrijving"></textarea>

                <input type="submit" value="Nieuwsbericht toevoegen">
                <a href="nieuws.php" class="terug-knop">Terug naar overzicht</a>
            </form>
        </main>

        <?php include "includes/footer.php"; ?>
    </div>

    <!-- Scripts onderaan de pagina -->
    <script src="https://cdn.tiny.cloud/1/1sc7z836e8nut9yj68rsb7xnvadqyxxjzcu93x5va01l0ykp/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        tinymce.init({
            selector: '#beschrijving',
            menubar: false,
            plugins: 'lists advlist',
            toolbar: 'undo redo | styleselect | bold italic underline | bullist numlist',
            branding: false,
            height: 300
        });

        // Formulier valideren vóór verzenden
        const form = document.querySelector("form");
        form.addEventListener("submit", function (e) {
            const content = tinymce.get("beschrijving").getContent({ format: "text" }).trim();
            if (content === "") {
                alert("Beschrijving mag niet leeg zijn.");
                e.preventDefault(); // Stop formulierverzending
            }
        });
    });
    </script>
</body>
</html>
