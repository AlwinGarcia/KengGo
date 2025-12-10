<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Trip - Processing</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body class="dashboard-page">
<?php
/**
 * Add Trip - PHP Form Handler
 * This file processes the form submission and adds trip to database
 */

// Include database connection and Trip model
require_once __DIR__ . '/../../../../includes/db_connect.php';
require_once __DIR__ . '/../../../model/Trip.php';

// Start output buffering
ob_start();

$success = false;
$message = '';
$errors = [];

try {
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // Validate required fields
        $required_fields = [
            'shuttle_number' => 'Shuttle Number',
            'from_address' => 'From Address',
            'to_address' => 'To Address',
            'trip_date' => 'Trip Date',
            'depart_time' => 'Departure Time',
            'price' => 'Price'
        ];
        
        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors[] = "$label is required";
            }
        }
        
        // Validate price
        if (!empty($_POST['price']) && (!is_numeric($_POST['price']) || $_POST['price'] <= 0)) {
            $errors[] = "Price must be a positive number";
        }
        
        // Validate seats
        if (!empty($_POST['seats']) && (!is_numeric($_POST['seats']) || $_POST['seats'] <= 0)) {
            $errors[] = "Seats must be a positive number";
        }
        
        // Validate date
        if (!empty($_POST['trip_date'])) {
            $date = DateTime::createFromFormat('Y-m-d', $_POST['trip_date']);
            if (!$date || $date->format('Y-m-d') !== $_POST['trip_date']) {
                $errors[] = "Invalid date format";
            }
        }
        
        // If no errors, proceed with insertion
        if (empty($errors)) {
            // Create Trip object
            $trip = new Trip($conn);
            
            // Set trip properties
            $trip->shuttle_number = $_POST['shuttle_number'];
            $trip->driver_name = $_POST['driver_name'] ?? null;
            $trip->driver_id = !empty($_POST['driver_id']) ? (int)$_POST['driver_id'] : null;
            $trip->plate_number = $_POST['plate_number'] ?? null;
            $trip->seats_available = !empty($_POST['seats']) ? (int)$_POST['seats'] : 20;
            $trip->from_address = $_POST['from_address'];
            $trip->to_address = $_POST['to_address'];
            $trip->trip_date = $_POST['trip_date'];
            $trip->depart_time = $_POST['depart_time'];
            $trip->arrive_time = $_POST['arrive_time'] ?? null;
            $trip->price = (float)$_POST['price'];
            $trip->status = $_POST['status'] ?? 'active';
            $trip->notes = $_POST['notes'] ?? null;
            
            // Create the trip
            if ($trip->create()) {
                $success = true;
                $message = "Trip added successfully!";
                $trip_id = $trip->id;
                
                // Log for debugging
                error_log("Trip created successfully with ID: " . $trip_id);
            } else {
                $errors[] = "Failed to create trip. Please try again.";
                error_log("Failed to create trip in database");
            }
        }
    } else {
        $errors[] = "Invalid request method";
    }
    
} catch (Exception $e) {
    $errors[] = $e->getMessage();
    error_log('Add Trip Error: ' . $e->getMessage());
} catch (Throwable $e) {
    $errors[] = "An unexpected error occurred";
    error_log('Add Trip Fatal Error: ' . $e->getMessage());
}

?>

<main class="dashboard-shell">
    <div style="padding: 40px 20px; text-align: center;">
        <?php if ($success): ?>
            <div style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 20px; box-shadow: 0 8px 24px rgba(34, 197, 94, 0.3);">
                <div style="font-size: 48px; margin-bottom: 10px;">✓</div>
                <h2 style="margin: 0 0 10px 0; font-size: 24px;">Success!</h2>
                <p style="margin: 0; font-size: 16px; opacity: 0.95;"><?php echo htmlspecialchars($message); ?></p>
                <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px; font-size: 14px;">
                    <strong>Trip Details:</strong><br>
                    Shuttle: <?php echo htmlspecialchars($_POST['shuttle_number']); ?><br>
                    From: <?php echo htmlspecialchars($_POST['from_address']); ?><br>
                    To: <?php echo htmlspecialchars($_POST['to_address']); ?><br>
                    Date: <?php echo htmlspecialchars($_POST['trip_date']); ?><br>
                    Time: <?php echo htmlspecialchars($_POST['depart_time']); ?>
                </div>
            </div>
            
            <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
                <a href="add.html" style="display: inline-block; padding: 14px 24px; background: white; color: #494949; border-radius: 12px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 16px rgba(0,0,0,0.1); transition: all 0.3s;">
                    ➕ Add Another Trip
                </a>
                <a href="dashboard.html" style="display: inline-block; padding: 14px 24px; background: linear-gradient(135deg, #1F41BB 0%, #3a5fd8 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 16px rgba(31,65,187,0.3); transition: all 0.3s;">
                    🏠 Go to Dashboard
                </a>
            </div>
            
            <script>
                // Auto redirect after 3 seconds
                setTimeout(function() {
                    window.location.href = 'dashboard.html';
                }, 3000);
            </script>
            
        <?php else: ?>
            <div style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; padding: 30px; border-radius: 16px; margin-bottom: 20px; box-shadow: 0 8px 24px rgba(239, 68, 68, 0.3);">
                <div style="font-size: 48px; margin-bottom: 10px;">✗</div>
                <h2 style="margin: 0 0 10px 0; font-size: 24px;">Error</h2>
                <p style="margin: 0; font-size: 16px; opacity: 0.95;">Failed to add trip</p>
                <?php if (!empty($errors)): ?>
                    <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.1); border-radius: 10px; text-align: left;">
                        <ul style="margin: 0; padding-left: 20px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            
            <a href="add.html" style="display: inline-block; padding: 14px 24px; background: linear-gradient(135deg, #1F41BB 0%, #3a5fd8 100%); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; box-shadow: 0 4px 16px rgba(31,65,187,0.3); transition: all 0.3s;">
                ← Go Back and Try Again
            </a>
        <?php endif; ?>
    </div>
</main>

</body>
</html>
