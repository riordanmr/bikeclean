<?php
require_once '../config-bikeclean.php';
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
    'kickstand_tighten',
    'seat_inspect',
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

// Build UPDATE query
$updateParts = [];
foreach ($repairFields as $field) {
    if (isset($_POST[$field])) {
        $value = intval($_POST[$field]);
        $updateParts[] = "$field = $value";
    }
}

if (empty($updateParts)) {
    echo json_encode(['success' => false, 'error' => 'No fields to update']);
    exit();
}

$sql = "UPDATE bikes SET " . implode(', ', $updateParts) . " WHERE id = $bikeId";

if ($conn->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Bike updated successfully',
        'bike_id' => $bikeId
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $conn->error
    ]);
}

$conn->close();
?>
