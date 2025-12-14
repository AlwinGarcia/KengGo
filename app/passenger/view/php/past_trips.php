<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Past Trips</title>
    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/dashboard.css">

    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/past_trips.css"> <!-- ✅ new file -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="dashboard-container">
    <!-- Header -->
    <div class="header">
        <span class="user">Past Trips</span>
        <span class="notif"><i class="fa-regular fa-clock"></i></span>
    </div>

    <!-- Trip Table Section -->
    <div class="trips-section">
        <div class="trips-title">Your Completed Trips</div>

        <?php if (empty($trips)): ?>
            <p class="no-trips">No past trips found.</p>
        <?php else: ?>
            <table class="trip-table">
                <thead>
                <tr>
                    <th>Shuttle #</th>
                    <th>Route</th>
                    <th>Date</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Price</th>
                    <th>Driver</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($trips as $trip): ?>
                    <tr>
                        <td><?= htmlspecialchars($trip['shuttle_number'] ?? '') ?></td>
                        <td><?= htmlspecialchars($trip['route'] ?? '') ?></td>
                        <td><?= htmlspecialchars($trip['trip_date'] ?? '') ?></td>
                        <td><?= htmlspecialchars($trip['departure_time'] ?? '') ?></td>
                        <td><?= htmlspecialchars($trip['arrival_time'] ?? '') ?></td>
                        <td>₱<?= number_format($trip['price'] ?? 0, 2) ?></td>
                        <td><?= htmlspecialchars($trip['driver_name'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Navigation Buttons -->
    <div class="dashboard-buttons">
        <a href="index.php?page=dashboard" class="btn-primary">
            <i class="fa-solid fa-house"></i> Back to Dashboard
        </a>
    </div>
</div>
</body>
</html>
