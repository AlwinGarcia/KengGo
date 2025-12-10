<?php
require_once __DIR__ . '/../../../includes/Session.php';
require_once __DIR__ . '/../../../includes/db_connect.php';
require_once __DIR__ . '/../../model/Trip.php';

$session = new Session();

if (!$session->isLoggedIn() || $session->get('role') !== 'passenger') {
    header("Location: index.php?page=login");
    exit();
}

$tripModel = new Trip($db);
$tripId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tripId <= 0) {
    header("Location: index.php?page=trips");
    exit();
}

$trip = $tripModel->getTripById($tripId);

if (empty($trip)) {
    header("Location: index.php?page=trips");
    exit();
}

// Check if passenger already booked this trip
$passengerId = $session->get('user_id');
$bookingCheck = $db->query("SELECT id FROM bookings WHERE passenger_id = $passengerId AND shuttle_id = $tripId");
$alreadyBooked = $bookingCheck->num_rows > 0;

include __DIR__ . '/../../../shared_layout/header.php';
include __DIR__ . '/../../../shared_layout/nav.php';
?>

<div class="trip-detail-container">
    <div class="back-link">
        <a href="?page=trips">&larr; Back to Trips</a>
    </div>
    
    <h1>Trip Details</h1>
    
    <div class="detail-card">
        <div class="detail-header">
            <h2><?= htmlspecialchars($trip['shuttle_number']) ?></h2>
            <span class="status-badge status-<?= strtolower($trip['status']) ?>">
                <?= ucfirst($trip['status']) ?>
            </span>
        </div>
        
        <div class="detail-grid">
            <div class="detail-item">
                <span class="label">Plate Number:</span>
                <span class="value"><?= htmlspecialchars($trip['plate_number'] ?? 'N/A') ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">Route:</span>
                <span class="value"><?= htmlspecialchars($trip['route'] ?? ($trip['from_address'] . ' to ' . $trip['to_address'])) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">From:</span>
                <span class="value"><?= htmlspecialchars($trip['from_address']) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">To:</span>
                <span class="value"><?= htmlspecialchars($trip['to_address']) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">Date:</span>
                <span class="value"><?= date('F d, Y', strtotime($trip['trip_date'])) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">Departure Time:</span>
                <span class="value"><?= date('h:i A', strtotime($trip['depart_time'])) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">Arrival Time:</span>
                <span class="value"><?= $trip['arrive_time'] ? date('h:i A', strtotime($trip['arrive_time'])) : 'N/A' ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">Capacity:</span>
                <span class="value"><?= $trip['capacity'] ?? $trip['seats_available'] ?> seats</span>
            </div>
            
            <div class="detail-item">
                <span class="label">Available Seats:</span>
                <span class="value"><?= $trip['seats_available'] ?> seats</span>
            </div>
            
            <div class="detail-item">
                <span class="label">Price:</span>
                <span class="value price">₱<?= number_format($trip['price'], 2) ?></span>
            </div>
            
            <div class="detail-item">
                <span class="label">Driver:</span>
                <span class="value"><?= htmlspecialchars($trip['driver_name'] ?? 'Not Assigned') ?></span>
            </div>
            
            <?php if (!empty($trip['notes'])): ?>
            <div class="detail-item full-width">
                <span class="label">Notes:</span>
                <span class="value"><?= nl2br(htmlspecialchars($trip['notes'])) ?></span>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="action-buttons">
            <?php if ($alreadyBooked): ?>
                <p class="already-booked">✓ You have already booked this trip</p>
                <a href="?page=booked_trips" class="btn-secondary">View My Bookings</a>
            <?php elseif ($trip['seats_available'] > 0 && $trip['status'] === 'active'): ?>
                <a href="?page=seat_management&trip_id=<?= $trip['id'] ?>" class="btn-primary">Book This Trip</a>
            <?php else: ?>
                <p class="unavailable">This trip is currently unavailable for booking</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../../shared_layout/footer.php'; ?>
