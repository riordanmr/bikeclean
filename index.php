<?php
require_once '../../config/config-bikeclean.php';
$conn = getDBConnection();

// Get counts for dashboard
$mechanicsCount = $conn->query("SELECT COUNT(*) as count FROM mechanics")->fetch_assoc()['count'];
$bikesCount = $conn->query("SELECT COUNT(*) as count FROM bikes")->fetch_assoc()['count'];
$activeBikesCount = $conn->query("SELECT COUNT(*) as count FROM bikes WHERE mechanic_id IS NOT NULL")->fetch_assoc()['count'];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BikeClean - Bike Repair Checklist</title>
    <link rel="icon" type="image/svg+xml" href="favicon.svg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header class="main-header">
            <h1>🚴 BikeClean</h1>
            <p class="subtitle">Bike Repair Checklist System</p>
        </header>

        <div class="dashboard">
            <div class="dashboard-card">
                <h3>📊 Dashboard</h3>
                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $mechanicsCount; ?></span>
                        <span class="stat-label">Mechanics</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $bikesCount; ?></span>
                        <span class="stat-label">Total Bikes</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $activeBikesCount; ?></span>
                        <span class="stat-label">Assigned Bikes</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-menu">
            <a href="mechanics.php" class="menu-card">
                <div class="menu-icon">👷</div>
                <h2>Manage Mechanics</h2>
                <p>Add, edit, and remove mechanics</p>
            </a>

            <a href="bikes.php" class="menu-card">
                <div class="menu-icon">🚲</div>
                <h2>Manage Bikes</h2>
                <p>Add, edit, and assign bikes to mechanics</p>
            </a>
        </div>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> BikeClean. Mobile-optimized bike repair tracking.</p>
        </footer>
    </div>
</body>
</html>
