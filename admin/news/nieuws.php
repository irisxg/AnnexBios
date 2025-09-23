<?php
require 'db.php';
session_start();

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

$array_nieuws = [];

$stmt = $conn->prepare("SELECT id, titel, afbeelding, publiceerdatum, beschrijving FROM nieuws ORDER BY publiceerdatum DESC");

$stmt->execute();
$result = $stmt->get_result();

$array_nieuws = [];
while ($row = $result->fetch_assoc()) {
    $array_nieuws[] = $row;
}


mysqli_close($conn);
?>

<!doctype html>
<html class="no-js" lang="nl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Annexbios Nieuws</title>
    <link rel="stylesheet" href="assets/nieuws.css">
    <meta name="theme-color" content="#ffffffff">
</head>

<?php include './includes/header.php'; ?>

<body>
    <div id="content">

        <main class="gloedpagina">
            <br><br>
            <h2><a href="add_nieuws.php" class="toevoeg-link">NIEUWS TOEVOEGEN</a></h2>


            <?php
            // TONEN VAN NIEUWSBERICHTEN
            foreach ($array_nieuws as $nieuws) {
            ?>
                <div class="nieuws-item">
                    <h2><a href="nieuwsdetail.php?id=<?php echo $nieuws['id']; ?>"><?php echo $nieuws['titel']; ?></h2>
                    <div class="nieuws-content">
                        <div class="links">
                            <img src="assets/img/<?php echo $nieuws['afbeelding']; ?>" alt="<?php echo $nieuws['titel']; ?>">
                            <p class="datum">Geproduceerd op: <?php echo date("d-m-Y", strtotime($nieuws['publiceerdatum'])); ?></p>
                        </div>
                        <div class="beschrijving">
                            <p><?php echo $nieuws['beschrijving']; ?></p>
                        </div>
                    </div></a>
                </div>



            <?php
            }
            ?>

        </main>


    </div>
</body>
<?php include './includes/footer.php'; ?>

</html>