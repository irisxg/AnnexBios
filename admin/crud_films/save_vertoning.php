<?php
require '../database.sql/db.php';

// POST-velden ophalen en veilig checken
$id            = $_POST['id'] ?? null;
$vestiging_id  = $_POST['vestiging_id'] ?? null;
$zaal_id       = $_POST['zaal_id'] ?? null;
$movie_id      = $_POST['movie_id'] ?? null;
$starttijd     = $_POST['start_time'] ?? null;
$prijs_normaal = $_POST['price_normaal'] ?? null;
$prijs_kind    = $_POST['price_kind'] ?? null;
$prijs_senior  = $_POST['price_senior'] ?? null;

// Controleer of alle velden aanwezig zijn
if (
    !$vestiging_id || !$zaal_id || !$movie_id || !$starttijd ||
    $prijs_normaal === null || $prijs_kind === null || $prijs_senior === null
) {
    die("Fout: Formulier is incompleet of waarden ontbreken.");
}

// Dag van de week ophalen (0=Zondag ... 6=Zaterdag)
$day = date('w', strtotime($starttijd));

// Openingstijden ophalen
$stmt = $pdo->prepare("SELECT * FROM openingstijden WHERE vestiging_id = ? AND day_of_week = ?");
$stmt->execute([$vestiging_id, $day]);
$ot = $stmt->fetch();

if (!$ot || !$ot['open_time'] || !$ot['close_time']) {
    die("Fout: Vestiging gesloten op deze dag.");
}

// DateTime objecten voor start, open en close
$startTime = new DateTime($starttijd);
$openTime  = new DateTime($startTime->format('Y-m-d') . ' ' . $ot['open_time']);
$closeTime = new DateTime($startTime->format('Y-m-d') . ' ' . $ot['close_time']);

// Handle sluiting na middernacht
if ($closeTime <= $openTime) {
    $closeTime->modify('+1 day');
}

// Controleer starttijd
if ($startTime < $openTime || $startTime > $closeTime) {
    die("Fout: Starttijd valt buiten openingstijden. Vestiging opent om " 
        . $openTime->format('H:i') . " en sluit om " 
        . $closeTime->format('H:i') . ".");
}

// Insert of update in database
if ($id) {
    $stmt = $pdo->prepare("UPDATE vertoningen 
                           SET vestiging_id=?, zaal_id=?, movie_id=?, starttijd=?, 
                               prijs_normaal=?, prijs_kind=?, prijs_senior=? 
                           WHERE id=?");
    $stmt->execute([
        $vestiging_id,
        $zaal_id,
        $movie_id,
        $starttijd,
        $prijs_normaal,
        $prijs_kind,
        $prijs_senior,
        $id
    ]);
} else {
    $stmt = $pdo->prepare("INSERT INTO vertoningen 
        (vestiging_id, zaal_id, movie_id, starttijd, prijs_normaal, prijs_kind, prijs_senior) 
        VALUES (?,?,?,?,?,?,?)");
    $stmt->execute([
        $vestiging_id,
        $zaal_id,
        $movie_id,
        $starttijd,
        $prijs_normaal,
        $prijs_kind,
        $prijs_senior
    ]);
}

// Terug naar de lijst
header("Location: list_vertoningen.php");
exit;
