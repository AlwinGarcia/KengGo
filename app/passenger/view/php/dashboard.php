<?php
$passengerName = $_SESSION['passenger_name'] ?? 'Guest';
$passengerId = $_SESSION['passenger_id'] ?? 'N/A';
$trips = $trips ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <span class="user">Hey, <?= htmlspecialchars($passengerName) ?></span>
            <span class="notif"><i class="fa-regular fa-bell"></i></span>
        </div>
        <div class="welcome-card">
            <span class="big-number">69</span>
            <div class="title">Welcome To KengGo!</div>
            <div class="details">Eagles Crest Phase 1<br>To Camp 4</div>
            <div class="pass-id">Pass id<br><?= htmlspecialchars($passengerId) ?></div>
        </div>
        <div class="trips-section">
            <div class="trips-title">
                Your last trips
                <span class="menu"><i class="fa-solid fa-bars"></i></span>
            </div>
            <div class="trips-grid">
                <?php if (!empty($trips)): ?>
                    <?php foreach ($trips as $trip): ?>
                        <button class="trip-btn">
                            <span class="trip-icon"><i class="fa-solid fa-bus"></i></span>
                            <span class="trip-name"><?= htmlspecialchars($trip['plate_number']) ?></span>
                            <span class="trip-from">From: <?= htmlspecialchars($trip['route']) ?></span>
                            <span class="trip-to">To: <?= htmlspecialchars($trip['destination']) ?></span>
                            <span class="trip-price">₱<?= htmlspecialchars($trip['price']) ?></span>
                        </button>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No trips found.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="bottom-nav">
            <span class="nav-icon"><i class="fa-solid fa-house"></i></span>
            <span class="nav-icon"><i class="fa-regular fa-location-dot"></i></span>
            <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <span class="nav-icon"><i class="fa-solid fa-user"></i></span>
            <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
        </div>
    </div>
</body>
</html>
