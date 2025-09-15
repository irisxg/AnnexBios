<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annexbios";
// Nieuwe database

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer verbinding
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Nieuws uit database halen
$array_nieuws = []; // Lege array om nieuwsberichten op te slaan

$stmt = $conn->prepare("SELECT id, titel, afbeelding, publiceerdatum, beschrijving FROM nieuws ORDER BY publiceerdatum DESC");

$stmt->execute();
$result = $stmt->get_result();

$array_nieuws = [];
while ($row = $result->fetch_assoc()) {
    $array_nieuws[] = $row;
}


// Verbinding sluiten
mysqli_close($conn);
?>

<!--Alles kababcase-->
<!--commets, classes ect. in nederlands-->
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
            <h2><a href="add-nieuws.php" class="toevoeg-link">NIEUWS TOEVOEGEN</a></h2>


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