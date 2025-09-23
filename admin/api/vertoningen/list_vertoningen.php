<?php
// get_vestiging.php
header('Content-Type: application/json');

require '../../middleware/auth.php'; // voert tokencheck uit en zet $currentVestiging

// Hier kun je nu veilig werken met $currentVestiging
echo json_encode([
    'message' => 'Toegang verleend',
    'vestiging' => $currentVestiging
]);