<?php
session_start();

// Verbinding maken met de juiste database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "annexbios";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Verbinding mislukt: " . $conn->connect_error);
}

// Controleren of er een ID is meegegeven
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Geen nieuws ID opgegeven.";
    exit;
}

$id = intval($_GET['id']);

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
        /* Detailpagina styling */
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

        .nieuwsdetail-container p {
            font-size: 1em;
            line-height: 1.6;
            color: #000;
            margin-bottom: 30px;
        }

        /* Knoppen */
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

        /* Responsive */
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

        textarea {
            width: 100%;
            height: 150px;
            resize: vertical;
            /* Of 'none' als je geen resizing wilt */
            overflow-wrap: break-word;
            word-wrap: break-word;
            white-space: pre-wrap;
            overflow-y: auto;
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
                    <?php echo nl2br(htmlspecialchars($nieuws['beschrijving'])); ?>

                </div>
            </div>

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