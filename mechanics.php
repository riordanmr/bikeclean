<?php
require_once '../../config/config-bikeclean.php';
$conn = getDBConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $full_name = $conn->real_escape_string($_POST['full_name']);
            $sql = "INSERT INTO mechanics (full_name) VALUES ('$full_name')";
            $conn->query($sql);
        } elseif ($_POST['action'] === 'edit') {
            $id = intval($_POST['id']);
            $full_name = $conn->real_escape_string($_POST['full_name']);
            $sql = "UPDATE mechanics SET full_name = '$full_name' WHERE id = $id";
            $conn->query($sql);
        } elseif ($_POST['action'] === 'delete') {
            $id = intval($_POST['id']);
            $sql = "DELETE FROM mechanics WHERE id = $id";
            $conn->query($sql);
        }
    }
    header("Location: mechanics.php");
    exit();
}

// Get all mechanics
$result = $conn->query("SELECT * FROM mechanics ORDER BY full_name");
$mechanics = $result->fetch_all(MYSQLI_ASSOC);

// Get mechanic for editing if ID is provided
$editMechanic = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $editResult = $conn->query("SELECT * FROM mechanics WHERE id = $editId");
    $editMechanic = $editResult->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Mechanics - BikeClean</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Manage Mechanics</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="bikes.php">Bikes</a>
            </nav>
        </header>

        <div class="form-section">
            <h2><?php echo $editMechanic ? 'Edit Mechanic' : 'Add New Mechanic'; ?></h2>
            <form method="POST" action="mechanics.php">
                <input type="hidden" name="action" value="<?php echo $editMechanic ? 'edit' : 'add'; ?>">
                <?php if ($editMechanic): ?>
                    <input type="hidden" name="id" value="<?php echo $editMechanic['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" 
                           value="<?php echo $editMechanic ? htmlspecialchars($editMechanic['full_name']) : ''; ?>" 
                           required>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editMechanic ? 'Update' : 'Add'; ?> Mechanic
                    </button>
                    <?php if ($editMechanic): ?>
                        <a href="mechanics.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="list-section">
            <h2>All Mechanics</h2>
            <?php if (empty($mechanics)): ?>
                <p class="empty-message">No mechanics found. Add one above.</p>
            <?php else: ?>
                <div class="list">
                    <?php foreach ($mechanics as $mechanic): ?>
                        <div class="list-item">
                            <div class="item-content">
                                <strong><?php echo htmlspecialchars($mechanic['full_name']); ?></strong>
                                <small>ID: <?php echo $mechanic['id']; ?></small>
                            </div>
                            <div class="item-actions">
                                <a href="mechanics.php?edit=<?php echo $mechanic['id']; ?>" class="btn btn-small btn-secondary">Edit</a>
                                <form method="POST" action="mechanics.php" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to delete this mechanic?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $mechanic['id']; ?>">
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
