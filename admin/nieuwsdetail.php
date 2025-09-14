<?php
session_start();

// Verbinding maken met de juiste database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annexbios"; // Let op: dit moet dezelfde database zijn als je nieuws

$conn = new mysqli($servername, $username, $password, $dbname);

// Verbinding controleren
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
$stmt = $conn->prepare("SELECT titel, afbeelding, publiceerdatum, beschrijving FROM nieuws WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    echo "Nieuwsbericht niet gevonden.";
    exit;
}

$nieuws = $result->fetch_assoc();

// Verwijderen van nieuwsbericht als formulier is ingediend
if (isset($_POST['delete'])) {
    $delete_sql = "DELETE FROM nieuws WHERE id = $id";
    if ($conn->query($delete_sql) === TRUE) {
        // Redirect naar overzichtspagina na verwijderen
        header("Location: nieuws.php");
        exit();
    } else {
        echo "Fout bij verwijderen: " . $conn->error;
    }
}

?>

<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($nieuws['titel']); ?></title>
    <link rel="stylesheet" href="assets/nieuws.css">
</head>

<body>
    <?php include './includes/header.php'; ?>

    <main>
        <div class="nieuws-detail">
            <h1><?php echo htmlspecialchars($nieuws['titel']); ?></h1>
            <img src="assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="<?php echo htmlspecialchars($nieuws['titel']); ?>">
            <p class="datum">Geplaatst op: <?php echo date("d-m-Y", strtotime($nieuws['publiceerdatum'])); ?></p>
            <p><?php echo nl2br(htmlspecialchars($nieuws['beschrijving'])); ?></p>
        </div>

        <!-- Link om nieuwsbericht aan te passen -->
        <a href="edit-nieuws.php?id=<?php echo $id; ?>" class="terug-knop">Nieuwsbericht aanpassen</a>
        <!-- Verwijderknop met bevestiging -->
        <form method="post" onsubmit="return confirm('Weet je zeker dat je dit nieuwsbericht wilt verwijderen?');">
            <button type="submit" name="delete" class="verwijderen-knop">Nieuwsbericht verwijderen</button>
        </form>


    </main>

    <?php include './includes/footer.php'; ?>
</body>

</html>

<?php $conn->close(); ?>