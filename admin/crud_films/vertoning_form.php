<?php
require '../database.sql/db.php';

$vertoning = null;
$vestigingen = $pdo->query("SELECT * FROM vestigingen ORDER BY name")->fetchAll();
$films = $pdo->query("SELECT * FROM movies ORDER BY title")->fetchAll();

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM vertoningen WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $vertoning = $stmt->fetch();
}

// Zalen ophalen voor de juiste vestiging (bij edit)
$zalen = [];
if ($vertoning) {
    $stmt = $pdo->prepare("SELECT * FROM zalen WHERE vestiging_id = ?");
    $stmt->execute([$vertoning['vestiging_id']]);
    $zalen = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?php echo $vertoning ? "Vertoning bewerken" : "Nieuwe vertoning"; ?></title>
    <link rel="stylesheet" href="admin_vertoning_form.css">
    <script>
        async function loadZalen(vestigingId) {
            let response = await fetch('zalen_api.php?vestiging_id=' + vestigingId);
            let zalen = await response.json();
            let zaalSelect = document.getElementById('zaalSelect');
            zaalSelect.innerHTML = '';
            zalen.forEach(z => {
                let opt = document.createElement('option');
                opt.value = z.id;
                opt.textContent = z.name + " (" + z.capacity + " plaatsen)";
                zaalSelect.appendChild(opt);
            });
        }
    </script>
</head>
<body>
<h1><?php echo $vertoning ? "Vertoning bewerken" : "Nieuwe vertoning"; ?></h1>
<a href="list_vertoningen.php" class="btn-terug-vestiging-form">← Terug naar overzicht</a>

<form action="save_vertoning.php" method="post">
    <input type="hidden" name="id" value="<?php echo $vertoning['id'] ?? ''; ?>">

    <label>Vestiging:</label>
    <select name="vestiging_id" onchange="loadZalen(this.value)" required>
        <option value="">-- Kies vestiging --</option>
        <?php foreach ($vestigingen as $v): ?>
            <option value="<?php echo $v['id']; ?>" <?php echo ($vertoning && $vertoning['vestiging_id']==$v['id'])?'selected':''; ?>>
                <?php echo htmlspecialchars($v['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <label>Zaal:</label>
    <select name="zaal_id" id="zaalSelect" required>
        <?php foreach ($zalen as $z): ?>
            <option value="<?php echo $z['id']; ?>" <?php echo ($vertoning && $vertoning['zaal_id']==$z['id'])?'selected':''; ?>>
                <?php echo htmlspecialchars($z['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <label>Film:</label>
    <select name="movie_id" required>
        <option value="">-- Kies film --</option>
        <?php foreach ($films as $f): ?>
            <option value="<?php echo $f['id']; ?>" <?php echo ($vertoning && $vertoning['movie_id']==$f['id'])?'selected':''; ?>>
                <?php echo htmlspecialchars($f['title']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <label>Starttijd:</label>
    <input type="datetime-local" name="start_time"
           value="<?php echo $vertoning ? date('Y-m-d\TH:i', strtotime($vertoning['starttijd'])) : ''; ?>" required>
    <br>

    <label>Prijs (€):</label>
    <input type="number" name="price" step="0.01"
           value="<?php echo $vertoning['prijs'] ?? ''; ?>" required>
    <br><br>

    <button type="submit">Opslaan</button>
</form>
</body>
</html>