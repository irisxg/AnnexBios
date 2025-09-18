<?php
session_start();

// Verbinding met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annexbios"; // Database voor bioscoop

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleren of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Geen nieuws ID opgegeven.";
    exit;
}
$id = intval($_GET['id']); // Veilig maken van ID

// Nieuwsbericht ophalen
$sql = "SELECT * FROM nieuws WHERE id = $id";
$result = $conn->query($sql);
if (!$result || $result->num_rows == 0) {
    echo "Nieuwsbericht niet gevonden.";
    exit;
}
$nieuws = $result->fetch_assoc();

// Nieuwsbericht bijwerken als formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titel = $_POST["titel"];
    $publiceerdatum = $_POST["publiceerdatum"];
    $beschrijving = ($_POST["beschrijving"]);

    $afbeelding = $_POST["afbeelding"]; // Optioneel: kan ook via upload

    $update_sql = "UPDATE nieuws 
                   SET titel = '$titel', publiceerdatum = '$publiceerdatum', beschrijving = '$beschrijving', afbeelding = '$afbeelding' 
                   WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        // Redirect naar detailpagina na succesvolle update
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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nieuwsbericht aanpassen</title>
    <link rel="stylesheet" href="assets/nieuws.css">
   
</head>

<body>
    <div id="content">
        <?php include "includes/header.php"; ?>
        <main>

            <form method="post" class="formulier">
                <h2>Nieuwsbericht aanpassen</h2><br><br>

                <div class="form-group">
                    <label for="titel">Titel:</label>
                    <input type="text" id="titel" name="titel" value="<?php echo htmlspecialchars($nieuws['titel']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="publiceerdatum">Publiceerdatum:</label>
                    <input type="date" id="publiceerdatum" name="publiceerdatum" value="<?php echo htmlspecialchars($nieuws['publiceerdatum']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="afbeelding">Afbeeldingsnaam:</label>
                    <input type="text" id="afbeelding" name="afbeelding" value="<?php echo htmlspecialchars($nieuws['afbeelding']); ?>">
                </div>

                <div class="form-group">
                    <label for="beschrijving">Beschrijving:</label>
                    <textarea id="beschrijving" name="beschrijving" required wrap="soft"><?php echo htmlspecialchars($nieuws['beschrijving']); ?></textarea>
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
