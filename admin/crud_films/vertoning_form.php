<?php
require '../database.sql/db.php';
include '../includes/header.php';

$vertoning = null;
$vestigingen = $pdo->query("SELECT * FROM vestigingen ORDER BY name")->fetchAll();
$films = $pdo->query("SELECT * FROM movies ORDER BY title")->fetchAll();

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM vertoningen WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $vertoning = $stmt->fetch();
}


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
<body class="avf-body">
    <h1 class="avf-title"><?php echo $vertoning ? "Vertoning bewerken" : "Nieuwe vertoning"; ?></h1>
    <a href="../crud_films/list_vertoningen.php" class="back-btn">← Terug</a>

    <form action="save_vertoning.php" method="post" class="avf-form">
        <input type="hidden" name="id" value="<?php echo $vertoning['id'] ?? ''; ?>">

        <label class="avf-label">Vestiging:</label>
        <select name="vestiging_id" onchange="loadZalen(this.value)" class="avf-select" required>
            <option value="">-- Kies vestiging --</option>
            <?php foreach ($vestigingen as $v): ?>
                <option value="<?php echo $v['id']; ?>" <?php echo ($vertoning && $vertoning['vestiging_id']==$v['id'])?'selected':''; ?>>
                    <?php echo htmlspecialchars($v['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label class="avf-label">Zaal:</label>
        <select name="zaal_id" id="zaalSelect" class="avf-select" required>
            <?php foreach ($zalen as $z): ?>
                <option value="<?php echo $z['id']; ?>" <?php echo ($vertoning && $vertoning['zaal_id']==$z['id'])?'selected':''; ?>>
                    <?php echo htmlspecialchars($z['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label class="avf-label">Film:</label>
        <select name="movie_id" class="avf-select" required>
            <option value="">-- Kies film --</option>
            <?php foreach ($films as $f): ?>
                <option value="<?php echo $f['id']; ?>" <?php echo ($vertoning && $vertoning['movie_id']==$f['id'])?'selected':''; ?>>
                    <?php echo htmlspecialchars($f['title']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br>

        <label class="avf-label">Starttijd:</label>
        <input type="datetime-local" name="start_time" class="avf-input"
               value="<?php echo $vertoning ? date('Y-m-d\TH:i', strtotime($vertoning['starttijd'])) : ''; ?>" required>
        <br>

        <label class="avf-label">Prijs (€):</label>
        <input type="number" name="price" step="0.01" class="avf-input"
               value="<?php echo $vertoning['prijs'] ?? ''; ?>" required>
        <br><br>

        <button type="submit" class="avf-btn">Opslaan</button>
    </form>
</body>
<?php include '../includes/footer.php';?> 
</html>
