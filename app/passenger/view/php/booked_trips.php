<?php
$passengerName = $_SESSION['passenger_name'] ?? 'Guest';
$trips         = $trips ?? [];
$totalTrips    = $totalTrips ?? 0; // passed from controller
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
        <!-- Header -->
        <div class="header">
            <span class="user">Booked Trips for <?= htmlspecialchars($passengerName) ?></span>
            <span class="notif"><i class="fa-regular fa-bell"></i></span>
        </div>

        <!-- Booked Trips Section -->
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

                            <!-- ✅ Cancel Booking Button -->
                            <?php if ($trip['status'] === 'booked' && !empty($trip['booking_id'])): ?>
                                <form method="POST" action="index.php?page=booked-trips<?= $showAll ? '&all=1' : '' ?>">
                                    <input type="hidden" name="cancel_booking" value="<?= htmlspecialchars($trip['booking_id']) ?>">
                                    <button type="submit" class="btn-danger" onclick="return confirm('Cancel this booking?');">
                                        <i class="fa-solid fa-xmark"></i> Cancel Booking
                                    </button>
                                </form>
                            <?php endif; ?>
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

        <!-- ✅ Back to Dashboard Button -->
        <div class="booked-trips-btn">
            <a href="index.php?page=dashboard" class="btn-primary">
                <i class="fa-solid fa-house"></i> Back to Dashboard
            </a>
        </div>

        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <a href="index.php?page=dashboard" class="nav-icon"><i class="fa-solid fa-house"></i></a>
            <a href="index.php?page=booked-trips" class="nav-icon"><i class="fa-solid fa-clipboard-check"></i></a>
            <a href="index.php?page=past-trips" class="nav-icon"><i class="fa-solid fa-clock-rotate-left"></i></a>
            <a href="index.php?page=profile" class="nav-icon"><i class="fa-solid fa-user"></i></a>
            <a href="index.php?page=settings" class="nav-icon"><i class="fa-solid fa-gear"></i></a>
        </div>
    </div>
</body>
</html>
