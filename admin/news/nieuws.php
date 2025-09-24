<?php
session_start();
require '../database.sql/db.php';

// Nieuws ophalen
$array_nieuws = [];
$stmt = $conn->prepare("SELECT id, titel, afbeelding, publiceerdatum, beschrijving FROM nieuws ORDER BY publiceerdatum DESC");
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $array_nieuws[] = $row;
}

$conn->close();
?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annexbios Nieuws</title>
    <link rel="stylesheet" href="assets/nieuws.css">
    <meta name="theme-color" content="#ffffffff">
</head>
<body>
    <?php include './includes/header.php'; ?>

    <div id="content">
        <main class="gloedpagina">
            <br><br>
            <h2><a href="add_nieuws.php" class="toevoeg-link">NIEUWS TOEVOEGEN</a></h2>
            <br><br>

            <?php foreach ($array_nieuws as $nieuws): ?>
                <div class="nieuws-item">
                    <a href="nieuwsdetail.php?id=<?php echo $nieuws['id']; ?>">
                        <h2><?php echo htmlspecialchars($nieuws['titel']); ?></h2>
                        <div class="nieuws-content">
                            <div class="links">
                                <img src="assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="<?php echo htmlspecialchars($nieuws['titel']); ?>">
                                <p class="datum">Geproduceerd op: <?php echo date("d-m-Y", strtotime($nieuws['publiceerdatum'])); ?></p>
                            </div>
                            <div class="beschrijving" style="max-width: 600px; width: 100%;">
                                <div class="beschrijving-tekst">
                                    <?php echo $nieuws['beschrijving']; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </main>
    </div>

    <?php include './includes/footer.php'; ?>
</body>
</html>
