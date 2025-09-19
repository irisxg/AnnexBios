<?php
require '../database.sql/db.php';

$stmt = $pdo->query("SELECT * FROM vestigingen ORDER BY id");
$vestigingen = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Vestigingen</title>
    <link rel="stylesheet" href="admin_vestigingen_list.css">
</head>

<body class="admin-list-body">
    <h1 class="admin-list-title">Vestigingen</h1>

    <div class="admin-list-container">
        <a href="vestiging_form.php" class="admin-list-btn" style="margin-bottom:15px; display:inline-block;">+ Nieuwe vestiging</a>

        <?php foreach ($vestigingen as $v): ?>
            <div class="admin-list-card">
                <div>
                    <div class="admin-list-name"><?php echo htmlspecialchars($v['name']); ?></div>
                    <div class="admin-list-api"><?php echo htmlspecialchars($v['api_key']); ?></div>
                </div>
                <a href="vestiging_form.php?id=<?php echo $v['id']; ?>" class="admin-list-btn-edit">Bewerken</a>
                <a href="delete_vestiging.php?id=<?php echo $v['id']; ?>"
                    class="admin-list-btn-delete"
                    onclick="openDeleteModal(event, <?php echo $v['id']; ?>)">
                    verwijderen
                </a>

                <div id="deleteModal" class="modal">
                    <div class="modal-content">
                        <h2>Weet je zeker dat je dit wilt verwijderen?</h2>
                        <p>Deze actie kan niet ongedaan gemaakt worden.</p>
                        <div class="modal-actions">
                            <button onclick="closeDeleteModal()" class="btn-cancel">Nee, annuleren</button>
                            <a id="confirmDeleteBtn" href="#" class="btn-delete">Ja, verwijderen</a>
                        </div>
                    </div>
                </div>

                <script>
                    function openDeleteModal(event, id) {
                        event.preventDefault();
                        const modal = document.getElementById('deleteModal');
                        const confirmBtn = document.getElementById('confirmDeleteBtn');
                        confirmBtn.href = 'delete_vestiging.php?id=' + id;
                        modal.style.display = 'flex';
                    }

                    function closeDeleteModal() {
                        document.getElementById('deleteModal').style.display = 'none';
                    }
                </script>

            </div>
        <?php endforeach; ?>
    </div>

</body>

</html>