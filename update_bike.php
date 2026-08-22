<?php
require_once '../../config/config-bikeclean.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

if (!isset($_POST['bike_id'])) {
    echo json_encode(['success' => false, 'error' => 'Bike ID not provided']);
    exit();
}

$conn = getDBConnection();
$bikeId = intval($_POST['bike_id']);

// Define all repair item fields
$repairFields = [
    'frame_clean',
    'wheels_clean',
    'wheels_true',
    'spokes_clean',
    'seatpost_clean_grease',
    'kickstand_tighten',
    'seat_inspect',
    'wheelhubs_tighten',
    'tires_valve_stems',
    'tires_inflate',
    'rear_derailleur',
    'cassette_clean',
    'chain_clean',
    'chainrings_clean',
    'front_derailleur',
    'cranks',
    'pedals',
    'headset_tighten',
    'brakes',
    'reflectors_check',
    'chrome_clean'
];

// Build UPDATE query with prepared statements
$updateParts = [];
$params = [];
$types = '';

foreach ($repairFields as $field) {
    if (isset($_POST[$field])) {
        $updateParts[] = "$field = ?";
        $types .= 'i';
        $params[] = intval($_POST[$field]);
    }
}

if (array_key_exists('notes', $_POST)) {
    $updateParts[] = "notes = ?";
    $types .= 's';
    $params[] = trim($_POST['notes']);
}

if (empty($updateParts)) {
    echo json_encode(['success' => false, 'error' => 'No fields to update']);
    exit();
}

$sql = "UPDATE bikes SET " . implode(', ', $updateParts) . " WHERE id = ?";
$types .= 'i';
$params[] = $bikeId;

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $conn->error]);
    exit();
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Bike updated successfully',
        'bike_id' => $bikeId
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
