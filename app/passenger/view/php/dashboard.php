<?php
$passengerName = $_SESSION['passenger_name'] ?? 'Guest';
$passengerId   = $_SESSION['passenger_id'] ?? 'N/A';
$trips         = $trips ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Dashboard</title>
    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Header -->
        <div class="header">
            <span class="user">Hey, <?= htmlspecialchars($passengerName) ?></span>
            <span class="notif"><i class="fa-regular fa-bell"></i></span>
        </div>

        <!-- Welcome Card -->
        <div class="welcome-card">
            <span class="big-number">69</span>
            <div class="title">Welcome To KengGo!</div>
            <div class="details">Your Shuttle Service</div>
            <div class="pass-id">Pass id<br><?= htmlspecialchars($passengerId) ?></div>
        </div>

        <!-- Available Trips Section -->
        <div class="trips-section">
            <div class="trips-title">Available Trips</div>
            <div class="trips-grid">
                <?php if (!empty($trips)): ?>
                    <?php foreach ($trips as $trip): ?>
                        <a href="index.php?page=seat-management&shuttle_id=<?= htmlspecialchars($trip['shuttle_id'] ?? '') ?>" class="trip-btn">
                            <span class="trip-icon"><i class="fa-solid fa-bus"></i></span>
                            <span class="trip-name"><?= htmlspecialchars($trip['route'] ?? 'N/A') ?></span>
                            <span class="trip-date">Date: <?= htmlspecialchars($trip['trip_date'] ?? 'N/A') ?></span>
                            <span class="trip-time">
                                Depart: <?= htmlspecialchars($trip['departure_time'] ?? '') ?> |
                                Arrive: <?= htmlspecialchars($trip['arrival_time'] ?? '') ?>
                            </span>
                            <span class="trip-price">Fare: ₱<?= htmlspecialchars($trip['price'] ?? '0.00') ?></span>
                            <span class="trip-capacity">Seats: <?= htmlspecialchars($trip['capacity'] ?? '0') ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No active trips available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Centered Buttons -->
        <div class="dashboard-buttons">
            <a href="index.php?page=booked-trips" class="btn-primary">
                <i class="fa-solid fa-clipboard-check"></i> View Booked Trips
            </a>
            <a href="index.php?page=trips" class="btn-primary"> <!-- ✅ updated -->
                <i class="fa-solid fa-clock-rotate-left"></i> View Past Trips
            </a>
        </div>

        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <a href="index.php?page=dashboard" class="nav-icon"><i class="fa-solid fa-house"></i></a>
            <a href="index.php?page=seat-management&shuttle_id=1" class="nav-icon"><i class="fa-regular fa-location-dot"></i></a>
            <a href="index.php?page=booked-trips" class="nav-icon"><i class="fa-solid fa-clipboard-check"></i></a>
            <a href="index.php?page=past-trips" class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></a> <!-- ✅ updated -->
            <a href="index.php?page=profile" class="nav-icon"><i class="fa-solid fa-user"></i></a>
            <a href="index.php?page=settings" class="nav-icon"><i class="fa-solid fa-gear"></i></a>
        </div>
    </div>
</body>
</html>
