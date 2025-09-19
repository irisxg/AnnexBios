<?php
require '../database.sql/db.php';

$stmt = $pdo->query("
    SELECT v.id, v.starttijd, v.prijs,
           ve.name AS vestiging, z.name AS zaal, m.title AS film
    FROM vertoningen v
    JOIN vestigingen ve ON v.vestiging_id = ve.id
    JOIN zalen z ON v.zaal_id = z.id
    JOIN movies m ON v.movie_id = m.id
    ORDER BY v.starttijd
");
$vertoningen = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Vertoningen</title>
    <link rel="stylesheet" href="admin_vertoningen_list.css">
</head>
<body class="admin-vertoningen-body">

<h1 class="admin-vertoningen-title">Vertoningen</h1>

<div class="admin-vertoningen-container">
    <a href="vertoning_form.php" class="admin-vertoningen-btn">+ Nieuwe vertoning</a>

    <table class="admin-vertoningen-table">
        <tr>
            <th>Vestiging</th>
            <th>Zaal</th>
            <th>Film</th>
            <th>Starttijd</th>
            <th>Prijs</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($vertoningen as $v): ?>
            <tr>
                <td><?php echo htmlspecialchars($v['vestiging']); ?></td>
                <td><?php echo htmlspecialchars($v['zaal']); ?></td>
                <td><?php echo htmlspecialchars($v['film']); ?></td>
                <td><?php echo date('d-m-Y H:i', strtotime($v['starttijd'])); ?></td>
                <td>€ <?php echo number_format($v['prijs'], 2, ',', '.'); ?></td>
                <td class="admin-vertoningen-actions">
                    <a href="vertoning_form.php?id=<?php echo $v['id']; ?>" 
                       class="vertoning-btn-edit">Bewerken</a>
                    
                    <a href="javascript:void(0);" 
                       class="vertoning-btn-delete"
                       onclick="openVertoningDeleteModal(event, <?php echo $v['id']; ?>)">
                       Verwijderen
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<div id="vertoningDeleteModal" class="vertoning-modal">
  <div class="vertoning-modal-content">
    <h2>Weet je zeker dat je dit wilt verwijderen?</h2>
    <p>Deze actie kan niet ongedaan gemaakt worden.</p>
    <div class="vertoning-modal-actions">
      <button onclick="closeVertoningDeleteModal()" class="vertoning-btn-cancel">Nee, annuleren</button>
      <a id="vertoningConfirmDeleteBtn" href="#" class="vertoning-btn-delete">Ja, verwijderen</a>
    </div>
  </div>
</div>

<script>
function openVertoningDeleteModal(event, id) {
    event.preventDefault();
    const modal = document.getElementById('vertoningDeleteModal');
    const confirmBtn = document.getElementById('vertoningConfirmDeleteBtn');
    confirmBtn.href = 'delete_vertoning.php?id=' + id;
    modal.style.display = 'flex';
}

function closeVertoningDeleteModal() {
    document.getElementById('vertoningDeleteModal').style.display = 'none';
}
</script>

</body>
</html>
