<?php
session_start();
require '../database.sql/db.php';

// Controleren of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Geen nieuws ID opgegeven.";
    exit;
}
$id = intval($_GET['id']);

// Nieuwsbericht ophalen
$sql = "SELECT * FROM nieuws WHERE id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    echo "Nieuwsbericht niet gevonden.";
    exit;
}
$nieuws = $result->fetch_assoc();

// Formulierverwerking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"];
    $publiceerdatum = $_POST["publiceerdatum"];
    $beschrijving = $_POST["beschrijving"];
    $afbeelding = $nieuws['afbeelding']; // standaard behouden

    // Verwijderen van afbeelding via formulier
    if (isset($_POST['verwijder_afbeelding']) && $_POST['verwijder_afbeelding'] == '1') {
        if (!empty($afbeelding) && file_exists("uploads/" . $afbeelding)) {
            unlink("uploads/" . $afbeelding);
        }
        $afbeelding = '';
    }

    // Nieuwe afbeelding uploaden
    if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $bestandsnaam = basename($_FILES['afbeelding']['name']);
        $uploadPad = $uploadDir . $bestandsnaam;

        if (move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadPad)) {
            // Oude afbeelding verwijderen
            if (!empty($nieuws['afbeelding']) && file_exists($uploadDir . $nieuws['afbeelding'])) {
                unlink($uploadDir . $nieuws['afbeelding']);
            }
            $afbeelding = $bestandsnaam;
        }
    }

    // Update uitvoeren
    $update_sql = "UPDATE nieuws 
                   SET titel = '$titel', publiceerdatum = '$publiceerdatum', beschrijving = '$beschrijving', afbeelding = '$afbeelding' 
                   WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: nieuwsdetail.php?id=$id");
        exit();
    } else {
        echo "Fout bij bijwerken: " . $conn->error;
    }
}

$conn->close();
?>
<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>Nieuwsbericht aanpassen</title>
    <link rel="stylesheet" href="assets/nieuws.css">
</head>

<body>
    <div id="content">
        <?php include "includes/header.php"; ?>
        <main>
            <form method="post" enctype="multipart/form-data" class="formulier">
                <h2>Nieuwsbericht aanpassen</h2><br><br>

                <div class="form-group">
                    <label for="titel">Titel:</label>
                    <input type="text" id="titel" name="titel" value="<?php echo htmlspecialchars($nieuws['titel']); ?>" required>
                </div>


                <?php if (empty($nieuws['afbeelding'])): ?>
                    <div class="form-group" id="upload-veld">
                        <label for="afbeelding">Nieuwe afbeelding uploaden:</label>
                        <input type="file" id="afbeelding" name="afbeelding">
                    </div>
                <?php endif; ?>

                <?php if (!empty($nieuws['afbeelding'])): ?>
                    <div class="afbeelding-container" id="afbeelding-container">
                        <div class="afbeelding-header">
                            <span>Huidige afbeelding</span>
                            <button type="button" onclick="verwijderAfbeelding()">✖</button>
                        </div>
                        <img src="uploads/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="Afbeelding" class="voorbeeld-afbeelding">
                        <input type="hidden" name="verwijder_afbeelding" id="verwijder_afbeelding" value="0">
                    </div>
                <?php endif; ?> <br>



                <script>
                    function verwijderAfbeelding() {
                        document.getElementById('verwijder_afbeelding').value = '1';
                        const container = document.getElementById('afbeelding-container');
                        if (container) container.remove();

                        // Voeg uploadveld toe vóór de publiceerdatum
                        const uploadHTML = `
            <div class="form-group" id="upload-veld">
                <label for="afbeelding">Nieuwe afbeelding uploaden:</label>
                <input type="file" id="afbeelding" name="afbeelding">
            </div>
        `;
                        const publiceerVeld = document.getElementById('publiceerdatum').closest('.form-group');
                        publiceerVeld.insertAdjacentHTML('beforebegin', uploadHTML);
                    }
                </script>


                <div class="form-group">
                    <label for="publiceerdatum">Publiceerdatum:</label>
                    <input type="date" id="publiceerdatum" name="publiceerdatum" value="<?php echo htmlspecialchars($nieuws['publiceerdatum']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="beschrijving">Beschrijving:</label>
                    <textarea id="beschrijving" name="beschrijving" required><?php echo htmlspecialchars($nieuws['beschrijving']); ?></textarea>
                </div>


                <div class="form-group">
                    <input type="submit" value="Opslaan">
                </div>

                <a href="nieuwsdetail.php?id=<?php echo $id; ?>" class="terug-knop">Terug naar nieuwsbericht</a>
            </form>
        </main>
        <?php include "includes/footer.php"; ?>
    </div>

</body>

</html>