<?php
session_start();

// Verbinding maken met de database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annexbios";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// ID ophalen en controleren
$id = intval($_GET['id'] ?? 0);
if ($id === 0) {
    echo "Geen geldig ID opgegeven.";
    exit;
}

// Afbeelding ophalen uit database
$sql = "SELECT afbeelding FROM nieuws WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $afbeelding = $row['afbeelding'];

    // Verwijder afbeelding uit assets/img/
    $pad = "assets/img/" . $afbeelding;
    if (!empty($afbeelding) && file_exists($pad)) {
        unlink($pad);
    }
}
$stmt->close();

// Verwijder nieuwsbericht uit database
$delete_sql = "DELETE FROM nieuws WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $id);
$delete_stmt->execute();
$delete_stmt->close();

$conn->close();

// Terug naar overzicht
header("Location: nieuws.php");
exit;
?>
