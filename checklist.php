<?php
require_once '../config-bikeclean.php';
$conn = getDBConnection();

// Get bike ID from URL
if (!isset($_GET['id'])) {
    header("Location: bikes.php");
    exit();
}

$bikeId = intval($_GET['id']);

// Get bike details
$result = $conn->query("
    SELECT b.*, m.full_name as mechanic_name 
    FROM bikes b 
    LEFT JOIN mechanics m ON b.mechanic_id = m.id 
    WHERE b.id = $bikeId
");

if ($result->num_rows === 0) {
    header("Location: bikes.php");
    exit();
}

$bike = $result->fetch_assoc();

// Define repair items
$repairItems = [
    'frame_clean' => 'Frame: Clean',
    'wheels_clean' => 'Wheels: Clean',
    'wheels_true' => 'Wheels: True',
    'spokes_clean' => 'Spokes: Clean',
    'kickstand_tighten' => 'Kickstand: Tighten',
    'seat_inspect' => 'Seat: Inspect',
    'tires_valve_stems' => 'Tires: Straighten Valve Stems',
    'tires_inflate' => 'Tires: Inflate',
    'rear_derailleur' => 'Rear Derailleur: Clean and Adjust',
    'cassette_clean' => 'Cassette: Clean',
    'chain_clean' => 'Chain: Clean',
    'chainrings_clean' => 'Chainrings: Clean',
    'front_derailleur' => 'Front Derailleur: Clean and Adjust',
    'cranks' => 'Cranks: Clean and Tighten',
    'pedals' => 'Pedals: Clean and Tighten',
    'headset_tighten' => 'Headset: Tighten',
    'brakes' => 'Brakes: Lubricate and Adjust',
    'reflectors_check' => 'Reflectors: Check for Front and Back',
    'chrome_clean' => 'Chrome: Clean with Scotch-Brite'
];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bike Checklist - BikeClean</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Bike Checklist</h1>
            <div id="postingIndicator" class="posting-indicator" style="display: none;">posting</div>
            <nav>
                <a href="index.php">Home</a>
                <a href="bikes.php">Back to Bikes</a>
            </nav>
        </header>

        <div class="bike-info">
            <h2><?php echo htmlspecialchars($bike['description']); ?></h2>
            <p><strong>ID:</strong> <?php echo $bike['id']; ?></p>
            <p><strong>Mechanic:</strong> <?php echo $bike['mechanic_name'] ? htmlspecialchars($bike['mechanic_name']) : 'Unassigned'; ?></p>
            <p id="save-status" class="save-status"></p>
        </div>

        <div class="checklist-section">
            <h3>Repair Items</h3>
            <form id="checklistForm">
                <input type="hidden" id="bikeId" value="<?php echo $bike['id']; ?>">
                
                <?php foreach ($repairItems as $field => $label): ?>
                    <div class="checkbox-item">
                        <input type="checkbox" 
                               id="<?php echo $field; ?>" 
                               name="<?php echo $field; ?>"
                               <?php echo $bike[$field] ? 'checked' : ''; ?>>
                        <label for="<?php echo $field; ?>"><?php echo htmlspecialchars($label); ?></label>
                    </div>
                <?php endforeach; ?>
            </form>

            <div class="progress-summary">
                <h3>Progress</h3>
                <div id="progressBar" class="progress-bar">
                    <div id="progressFill" class="progress-fill"></div>
                </div>
                <p id="progressText" class="progress-text"></p>
            </div>
        </div>
    </div>

    <script>
        const bikeId = document.getElementById('bikeId').value;
        const checkboxes = document.querySelectorAll('#checklistForm input[type="checkbox"]');
        const saveStatus = document.getElementById('save-status');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const postingIndicator = document.getElementById('postingIndicator');
        
        let hasChanges = false;
        let lastSavedState = {};
        let isSaving = false;
        
        // Initialize last saved state
        function initializeState() {
            checkboxes.forEach(checkbox => {
                lastSavedState[checkbox.name] = checkbox.checked;
            });
            updateProgress();
        }
        
        // Update progress bar
        function updateProgress() {
            const total = checkboxes.length;
            let completed = 0;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) completed++;
            });
            
            const percentage = Math.round((completed / total) * 100);
            progressFill.style.width = percentage + '%';
            progressText.textContent = `${completed} of ${total} items completed (${percentage}%)`;
        }
        
        // Check if form has changes
        function checkForChanges() {
            hasChanges = false;
            checkboxes.forEach(checkbox => {
                if (checkbox.checked !== lastSavedState[checkbox.name]) {
                    hasChanges = true;
                }
            });
        }
        
        // Save changes via AJAX
        function saveChanges() {
            if (!hasChanges || isSaving) {
                return;
            }
            
            isSaving = true;
            saveStatus.textContent = 'Saving...';
            saveStatus.className = 'save-status saving';
            
            // Show posting indicator
            postingIndicator.style.display = 'block';
            
            // Gather form data
            const formData = new FormData();
            formData.append('bike_id', bikeId);
            
            checkboxes.forEach(checkbox => {
                formData.append(checkbox.name, checkbox.checked ? '1' : '0');
            });
            
            // Send AJAX request
            fetch('update_bike.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Hide posting indicator
                postingIndicator.style.display = 'none';
                
                if (data.success) {
                    // Update last saved state
                    checkboxes.forEach(checkbox => {
                        lastSavedState[checkbox.name] = checkbox.checked;
                    });
                    hasChanges = false;
                    saveStatus.textContent = 'Saved ✓';
                    saveStatus.className = 'save-status success';
                    
                    // Clear success message after 3 seconds
                    setTimeout(() => {
                        if (!hasChanges) {
                            saveStatus.textContent = '';
                            saveStatus.className = 'save-status';
                        }
                    }, 3000);
                } else {
                    saveStatus.textContent = 'Error saving: ' + (data.error || 'Unknown error');
                    saveStatus.className = 'save-status error';
                }
                isSaving = false;
            })
            .catch(error => {
                // Hide posting indicator
                postingIndicator.style.display = 'none';
                
                console.error('Error:', error);
                saveStatus.textContent = 'Error saving changes';
                saveStatus.className = 'save-status error';
                isSaving = false;
            });
        }
        
        // Add event listeners to checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                checkForChanges();
                updateProgress();
                if (hasChanges) {
                    saveStatus.textContent = 'Unsaved changes';
                    saveStatus.className = 'save-status warning';
                }
            });
        });
        
        // Auto-save every 10 seconds
        setInterval(() => {
            if (hasChanges) {
                saveChanges();
            }
        }, 10000);
        
        // Initialize
        initializeState();
    </script>
</body>
</html>
