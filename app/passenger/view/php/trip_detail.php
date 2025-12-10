<?php
require_once __DIR__ . '/../../../db_connect.php';
require_once __DIR__ . '/../../../model/Trip.php';
require_once __DIR__ . '/../../../Session.php';

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

include __DIR__ . '/../../../shared_layout/header.php';
include __DIR__ . '/../../../shared_layout/nav.php';
?>

<link rel="stylesheet" href="app/passenger/view/css/trip_status.css">

<div class="content">
    <h1>Trip Details</h1>

    <div class="trip-details-card">
        <p><strong>Shuttle number:</strong> <?php echo htmlspecialchars($trip['shuttle_number']); ?></p>
        <p><strong>Plate number:</strong> <?php echo htmlspecialchars($trip['plate_number']); ?></p>
        <p><strong>Route:</strong> <?php echo htmlspecialchars($trip['route']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($trip['trip_date']); ?></p>
        <p><strong>Departure time:</strong> <?php echo htmlspecialchars($trip['departure_time']); ?></p>
        <p><strong>Arrival time:</strong> <?php echo htmlspecialchars($trip['arrival_time']); ?></p>
        <p><strong>Capacity:</strong> <?php echo htmlspecialchars($trip['capacity']); ?></p>
        <p><strong>Price:</strong> <?php echo number_format($trip['price'], 2); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($trip['status']); ?></p>
        <p><strong>Driver:</strong> <?php echo htmlspecialchars($trip['driver_name'] ?? ''); ?></p>
        <?php if (!empty($trip['notes'])): ?>
            <p><strong>Notes:</strong> <?php echo htmlspecialchars($trip['notes']); ?></p>
        <?php endif; ?>
    </div>

    <p><a href="index.php?page=trips">&laquo; Back to trips</a></p>
</div>

<?php include __DIR__ . '/../../../shared_layout/footer.php'; ?>
