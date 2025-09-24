<?php
session_start();
require '../database.sql/db.php';
include "../includes/header.php";

// Controleren of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Geen nieuws ID opgegeven.";
    exit;
}
$id = intval($_GET['id']);

// Nieuwsbericht ophalen
$sql = "SELECT * FROM nieuws WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) {
    echo "Nieuwsbericht niet gevonden.";
    exit;
}
$nieuws = $result->fetch_assoc();
$stmt->close();

// Formulierverwerking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"];
    $publiceerdatum = $_POST["publiceerdatum"];
    $samenvatting = $_POST["samenvatting"];
    $beschrijving = $_POST["beschrijving"];
    $afbeelding = $nieuws['afbeelding']; // standaard behouden

    $uploadDir = '../assets/img/';

    // Verwijderen van afbeelding via formulier
    if (isset($_POST['verwijder_afbeelding']) && $_POST['verwijder_afbeelding'] == '1') {
        if (!empty($afbeelding) && file_exists($uploadDir . $afbeelding)) {
            unlink($uploadDir . $afbeelding);
        }
        $afbeelding = '';
    }

    // Nieuwe afbeelding uploaden
    if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] == UPLOAD_ERR_OK) {
        $origineleNaam = basename($_FILES['afbeelding']['name']);
        $bestandstype = $_FILES['afbeelding']['type'];
        $toegestane_types = ["image/jpeg", "image/png", "image/gif"];

        if (!in_array($bestandstype, $toegestane_types)) {
            die("Ongeldig bestandstype. Alleen JPG, PNG en GIF zijn toegestaan.");
        }

        $uniekeNaam = uniqid() . "_" . $origineleNaam;
        $uploadPad = $uploadDir . $uniekeNaam;

        if (move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadPad)) {
            // Oude afbeelding verwijderen
            if (!empty($nieuws['afbeelding']) && file_exists($uploadDir . $nieuws['afbeelding'])) {
                unlink($uploadDir . $nieuws['afbeelding']);
            }
            $afbeelding = $uniekeNaam;
        }
    }

    // Update uitvoeren
    $update_sql = "UPDATE nieuws 
                   SET titel = ?, publiceerdatum = ?, samenvatting = ?, beschrijving = ?, afbeelding = ? 
                   WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssi", $titel, $publiceerdatum, $samenvatting, $beschrijving, $afbeelding, $id);

    if ($update_stmt->execute()) {
        header("Location: nieuwsdetail.php?id=$id");
        exit();
    } else {
        echo "Fout bij bijwerken: " . $conn->error;
    }

    $update_stmt->close();
}

$conn->close();
?>

<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nieuwsbericht aanpassen</title>
    <link rel="stylesheet" href="assets/nieuws.css">
</head>

<body>
    <div id="content">
        <main>
            <form method="post" enctype="multipart/form-data" class="formulier">
                <h2>Nieuwsbericht aanpassen</h2>

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
                <?php endif; ?>

                <script>
                    function verwijderAfbeelding() {
                        document.getElementById('verwijder_afbeelding').value = '1';
                        const container = document.getElementById('afbeelding-container');
                        if (container) container.remove();

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
                    <label for="samenvatting">Samenvatting:</label>
                    <textarea id="samenvatting" name="samenvatting"><?php echo $nieuws['samenvatting']; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="beschrijving">Beschrijving:</label>
                    <textarea id="beschrijving" name="beschrijving"><?php echo $nieuws['beschrijving']; ?></textarea>
                </div>

                <div class="form-group">
                    <input type="submit" value="Opslaan">
                </div>

                <a href="nieuwsdetail.php?id=<?php echo $id; ?>" class="terug-knop">Terug naar nieuwsbericht</a>
            </form>
        </main>

        <?php include "../includes/footer.php"; ?>
    </div>

    <!-- TinyMCE toevoegen -->
    <script src="https://cdn.tiny.cloud/1/1sc7z836e8nut9yj68rsb7xnvadqyxxjzcu93x5va01l0ykp/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            tinymce.init({
                selector: 'textarea#samenvatting, textarea#beschrijving',
                menubar: false,
                plugins: 'lists advlist link',
                toolbar: 'undo redo | styleselect | bold italic underline | bullist numlist | link',
                branding: false,
                height: 300
            });
        });
    </script>
</body>

</html>