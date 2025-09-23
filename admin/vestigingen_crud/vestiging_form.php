<?php
require '../database.sql/db.php';
include '../includes/header.php';

$vestiging = null;
$openingtijden = [];
$zalen = [];

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM vestigingen WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $vestiging = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT * FROM openingstijden WHERE vestiging_id = ? ORDER BY day_of_week");
    $stmt->execute([$_GET['id']]);
    $openingtijden = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT * FROM zalen WHERE vestiging_id = ?");
    $stmt->execute([$_GET['id']]);
    $zalen = $stmt->fetchAll();
}

$dagen = [
    0 => 'Maandag',
    1 => 'Dinsdag',
    2 => 'Woensdag',
    3 => 'Donderdag',
    4 => 'Vrijdag',
    5 => 'Zaterdag',
    6 => 'Zondag'
];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Vestiging <?php echo $vestiging ? "bewerken" : "toevoegen"; ?></title>
    <link rel="stylesheet" href="admin_vestiging_form.css">
    <script>
        function adminAddRepeater(containerId, templateId) {
            let container = document.getElementById(containerId);
            let index = container.children.length;
            let template = document.getElementById(templateId).innerHTML.replace(/__INDEX__/g, index);
            container.insertAdjacentHTML('beforeend', template);
        }
    </script>
</head>
<body class="admin-vestiging-body">
<<<<<<< Updated upstream

<a href="../vestigingen_crud/list_vestigingen.php" class="back-btn">← Terug</a>

<h1 class="admin-vestiging-title">
    <?php echo $vestiging ? "Vestiging bewerken" : "Nieuwe vestiging"; ?>
</h1>

=======
<h1 class="admin-vestiging-title"><?php echo $vestiging ? "Vestiging bewerken" : "Nieuwe vestiging"; ?></h1>

<a href="../vestigingen_crud/list_vestigingen.php" class="back-btn">← Terug</a>
>>>>>>> Stashed changes
<form class="admin-vestiging-form" action="save_vestiging.php" method="post">
    <input type="hidden" name="id" value="<?php echo $vestiging['id'] ?? ''; ?>">

    <label>Naam vestiging:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($vestiging['name'] ?? ''); ?>" required>

    <label>API Key:</label>
    <input type="text" name="api_key" value="<?php echo htmlspecialchars($vestiging['api_key'] ?? ''); ?>" required>
    
    <h3>Openingstijden</h3>
    <br>
    <div id="admin-openingstijden" class="admin-vestiging-repeater">
        <?php foreach ($openingtijden as $i => $ot): ?>
            <div class="admin-vestiging-card admin-vestiging-flex">
                <select name="openingstijden[<?php echo $i; ?>][day_of_week]" required>
                    <?php foreach ($dagen as $num => $dagNaam): ?>
                        <option value="<?php echo $num; ?>" <?php echo ($ot['day_of_week'] == $num) ? 'selected' : ''; ?>><?php echo $dagNaam; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="time" name="openingstijden[<?php echo $i; ?>][open_time]" value="<?php echo $ot['open_time']; ?>" placeholder="Open">
                <input type="time" name="openingstijden[<?php echo $i; ?>][close_time]" value="<?php echo $ot['close_time']; ?>" placeholder="Sluit">
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="admin-vestiging-btn-repeater" onclick="adminAddRepeater('admin-openingstijden','tplAdminOpeningstijd')">+ Openingstijd</button>

    <h3>Zalen</h3>
    <div id="admin-zalen" class="admin-vestiging-repeater">
        <?php foreach ($zalen as $i => $zaal): ?>
            <div class="admin-vestiging-card admin-vestiging-flex">
                <input type="text" name="zalen[<?php echo $i; ?>][name]" value="<?php echo htmlspecialchars($zaal['name']); ?>" placeholder="Naam zaal">
                <input type="number" name="zalen[<?php echo $i; ?>][capacity]" value="<?php echo $zaal['capacity']; ?>" placeholder="Capaciteit">
                <input type="text" name="zalen[<?php echo $i; ?>][type]" value="<?php echo htmlspecialchars($zaal['type']); ?>" placeholder="Type">
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" class="admin-vestiging-btn-repeater" onclick="adminAddRepeater('admin-zalen','tplAdminZaal')">+ Zaal</button>

    <br><br>
    <button type="submit" class="admin-vestiging-btn">Opslaan</button>
</form>

<!-- Templates -->
<template id="tplAdminOpeningstijd">
    <div class="admin-vestiging-card admin-vestiging-flex">
        <select name="openingstijden[__INDEX__][day_of_week]" required>
            <?php foreach ($dagen as $num => $dagNaam): ?>
                <option value="<?php echo $num; ?>"><?php echo $dagNaam; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="time" name="openingstijden[__INDEX__][open_time]" placeholder="Open">
        <input type="time" name="openingstijden[__INDEX__][close_time]" placeholder="Sluit">
    </div>
</template>

<template id="tplAdminZaal">
    <div class="admin-vestiging-card admin-vestiging-flex">
        <input type="text" name="zalen[__INDEX__][name]" placeholder="Naam zaal">
        <input type="number" name="zalen[__INDEX__][capacity]" placeholder="Capaciteit">
        <input type="text" name="zalen[__INDEX__][type]" placeholder="Type">
    </div>
</template>
</body>
<?php include '../includes/footer.php';?> 
</html>
