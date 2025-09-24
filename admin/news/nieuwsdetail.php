<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annexbios";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Geen nieuws ID opgegeven.";
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT titel, afbeelding, publiceerdatum, beschrijving FROM nieuws WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    echo "Nieuwsbericht niet gevonden.";
    exit;
}

$nieuws = $result->fetch_assoc();

if (isset($_POST['delete'])) {
    // Verwijder eerst de afbeelding uit de map
    $afbeelding = $nieuws['afbeelding'];
    $pad = "assets/img/" . $afbeelding;

    if (!empty($afbeelding) && file_exists($pad)) {
        unlink($pad);
    }

    // Verwijder daarna het nieuwsbericht uit de database
    $delete_sql = "DELETE FROM nieuws WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $id);

    if ($delete_stmt->execute()) {
        $delete_stmt->close();
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
    <style>
        main.nieuwsdetail {
            background-color: #75757523;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
            box-sizing: border-box;
        }

        .nieuwsdetail-container {
            max-width: 900px;
            width: 100%;
            background-color: transparent;
            padding: 20px;
            box-sizing: border-box;
        }

        .nieuwsdetail-container h1 {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }

        .nieuwsdetail-container img {
            width: 100%;
            max-width: 600px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .nieuwsdetail-container .datum {
            font-size: 0.9em;
            color: #777;
            margin-bottom: 20px;
        }

        .beschrijving {
            max-width: 700px;
            font-size: 1rem;
            line-height: 1.6;
            color: #000;
        }

        .beschrijving ol,
        .beschrijving ul {
            padding-left: 1.5em;
            margin-bottom: 1em;
        }

        .beschrijving strong {
            font-weight: bold;
        }

        .beschrijving em {
            font-style: italic;
        }

        .beschrijving span {
            text-decoration: underline;
        }

        .terug-knop,
        .verwijderen-knop {
            display: inline-block;
            padding: 10px 20px;
            margin-right: 10px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
        }

        .terug-knop {
            background-color: #FF8A9D;
            color: white;
        }

        .terug-knop:hover {
            background-color: #ff6b84;
        }

        .verwijderen-knop {
            background-color: #2c3e50;
            color: white;
        }

        .verwijderen-knop:hover {
            background-color: #b80000ff;
        }

        @media (max-width: 600px) {
            .nieuwsdetail-container img {
                max-width: 100%;
            }

            .terug-knop,
            .verwijderen-knop {
                display: block;
                width: 100%;
                margin-bottom: 10px;
                text-align: center;
            }
        }

        .link a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>

<body>
    <?php include './includes/header.php'; ?>

    <main class="nieuwsdetail">
        <div class="nieuwsdetail-container">

            <div class="link">
                <h3><a href="nieuws.php">&lt; Terug</a></h3>
            </div>

            <br><br>
            <div>
                <h1><?php echo htmlspecialchars($nieuws['titel']); ?></h1>
                <img src="assets/img/<?php echo htmlspecialchars($nieuws['afbeelding']); ?>" alt="<?php echo htmlspecialchars($nieuws['titel']); ?>">
                <p class="datum">Geplaatst op: <?php echo date("d-m-Y", strtotime($nieuws['publiceerdatum'])); ?></p>
                <div class="beschrijving">
                    <?php echo $nieuws['beschrijving']; ?>
                </div>
            </div>

            <br>
            <a href="edit_nieuws.php?id=<?php echo $id; ?>" class="terug-knop">Nieuwsbericht aanpassen</a><br><br>

            <form method="post" onsubmit="return confirm('Weet je zeker dat je dit nieuwsbericht wilt verwijderen?');">
                <button type="submit" name="delete" class="verwijderen-knop">Nieuwsbericht verwijderen</button>
            </form>
        </div>
    </main>

    <?php include './includes/footer.php'; ?>
</body>

</html>

<?php $conn->close(); ?>