<?php
require_once '../../config/config-bikeclean.php';
$conn = getDBConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $mechanic_id = $_POST['mechanic_id'] ? intval($_POST['mechanic_id']) : 'NULL';
            $description = $conn->real_escape_string($_POST['description']);
            $sql = "INSERT INTO bikes (mechanic_id, description) VALUES ($mechanic_id, '$description')";
            $conn->query($sql);
            
            // Save the selected mechanic to a cookie for next time
            if ($_POST['mechanic_id']) {
                setcookie('last_mechanic_id', $_POST['mechanic_id'], time() + (86400 * 180), '/'); 
            }
        } elseif ($_POST['action'] === 'edit') {
            $id = intval($_POST['id']);
            $mechanic_id = $_POST['mechanic_id'] ? intval($_POST['mechanic_id']) : 'NULL';
            $description = $conn->real_escape_string($_POST['description']);
            $sql = "UPDATE bikes SET mechanic_id = $mechanic_id, description = '$description' WHERE id = $id";
            $conn->query($sql);
        } elseif ($_POST['action'] === 'delete') {
            $id = intval($_POST['id']);
            $sql = "DELETE FROM bikes WHERE id = $id";
            $conn->query($sql);
        }
    }
    header("Location: bikes.php");
    exit();
}

// Get all mechanics for dropdown
$mechanicsResult = $conn->query("SELECT * FROM mechanics ORDER BY full_name");
$mechanics = $mechanicsResult->fetch_all(MYSQLI_ASSOC);

// Get all bikes
$result = $conn->query("
    SELECT b.*, m.full_name as mechanic_name 
    FROM bikes b 
    LEFT JOIN mechanics m ON b.mechanic_id = m.id 
    ORDER BY b.id DESC
");
$bikes = $result->fetch_all(MYSQLI_ASSOC);

// Get bike for editing if ID is provided
$editBike = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editResult = $conn->query("SELECT * FROM bikes WHERE id = $editId");
    $editBike = $editResult->fetch_assoc();
}

// Get last selected mechanic from cookie (for add form only)
$lastMechanicId = isset($_COOKIE['last_mechanic_id']) ? intval($_COOKIE['last_mechanic_id']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bikes - BikeClean</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Bikes</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="mechanics.php">Mechanics</a>
            </nav>
        </header>

        <div class="form-section">
            <h2><?php echo $editBike ? 'Edit Bike' : 'Add New Bike'; ?></h2>
            <form method="POST" action="bikes.php">
                <input type="hidden" name="action" value="<?php echo $editBike ? 'edit' : 'add'; ?>">
                <?php if ($editBike): ?>
                    <input type="hidden" name="id" value="<?php echo $editBike['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" id="description" name="description" 
                           value="<?php echo $editBike ? htmlspecialchars($editBike['description']) : ''; ?>" 
                           required placeholder="e.g., Red Trek Mountain Bike">
                </div>
                
                <div class="form-group">
                    <label for="mechanic_id">Assigned Mechanic:</label>
                    <select id="mechanic_id" name="mechanic_id">
                        <option value="">-- Unassigned --</option>
                        <?php foreach ($mechanics as $mechanic): ?>
                            <?php 
                                // For edit: use bike's mechanic; for add: use cookie value
                                $isSelected = $editBike 
                                    ? ($editBike['mechanic_id'] == $mechanic['id']) 
                                    : (!$editBike && $lastMechanicId && $lastMechanicId == $mechanic['id']);
                            ?>
                            <option value="<?php echo $mechanic['id']; ?>"
                                    <?php echo $isSelected ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mechanic['full_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editBike ? 'Update' : 'Add'; ?> Bike
                    </button>
                    <?php if ($editBike): ?>
                        <a href="bikes.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="list-section">
            <h2>All Bikes</h2>
            <?php if (empty($bikes)): ?>
                <p class="empty-message">No bikes found. Add one above.</p>
            <?php else: ?>
                <div class="list">
                    <?php foreach ($bikes as $bike): ?>
                        <div class="list-item">
                            <div class="item-content">
                                <strong><?php echo htmlspecialchars($bike['description']); ?></strong>
                                <small>
                                    ID: <?php echo $bike['id']; ?> | 
                                    Mechanic: <?php echo $bike['mechanic_name'] ? htmlspecialchars($bike['mechanic_name']) : 'Unassigned'; ?>
                                </small>
                            </div>
                            <div class="item-actions">
                                <a href="checklist.php?id=<?php echo $bike['id']; ?>" class="btn btn-small btn-primary">Checklist</a>
                                <a href="bikes.php?edit=<?php echo $bike['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                <form method="POST" action="bikes.php" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this bike?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $bike['id']; ?>">
                                    <button type="submit" class="btn btn-small btn-danger">Delete</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>
