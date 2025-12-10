<?php
$passengerName = $_SESSION['passenger_name'] ?? 'Guest';
$trips         = $trips ?? [];
$totalTrips    = $totalTrips ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booked Trips</title>
    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <span class="user">Booked Trips for <?= htmlspecialchars($passengerName) ?></span>
            <span class="notif"><i class="fa-regular fa-bell"></i></span>
        </div>

        <div class="trips-section">
            <div class="trips-title">
                <?= $showAll ? "All Booked Trips" : "Latest Booked Trips" ?>
            </div>

            <div class="trips-grid">
                <?php if (!empty($trips)): ?>
                    <?php foreach ($trips as $trip): ?>
                        <div class="trip-card">
                            <span class="trip-icon"><i class="fa-solid fa-bus"></i></span>
                            <span class="trip-route"><?= htmlspecialchars($trip['route']) ?></span>
                            <span class="trip-date">Date: <?= htmlspecialchars($trip['trip_date']) ?></span>
                            <span class="trip-time">
                                Depart: <?= htmlspecialchars($trip['departure_time']) ?> |
                                Arrive: <?= htmlspecialchars($trip['arrival_time']) ?>
                            </span>
                            <span class="trip-fare">Fare: ₱<?= htmlspecialchars($trip['price']) ?></span>
                            <span class="trip-seat">Seat #: <?= htmlspecialchars($trip['seat_number']) ?></span>
                            <span class="trip-status">Status: <?= htmlspecialchars($trip['status']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No booked trips found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- ✅ Toggle Buttons -->
        <div class="booked-trips-btn">
            <?php if (!$showAll && $totalTrips >= 7): ?>
                <a href="index.php?page=booked-trips&all=1" class="btn-primary">
                    <i class="fa-solid fa-list"></i> See All Bookings
                </a>
            <?php elseif ($showAll): ?>
                <a href="index.php?page=booked-trips" class="btn-primary">
                    <i class="fa-solid fa-rotate-left"></i> Back to Latest 6
                </a>
            <?php endif; ?>
        </div>

        <div class="booked-trips-btn">
            <a href="index.php?page=dashboard" class="btn-primary">
                <i class="fa-solid fa-house"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
