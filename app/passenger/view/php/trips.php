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

// Optional search functionality
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
if ($search !== '') {
    $like = '%' . $db->real_escape_string($search) . '%';
    $sql = "SELECT s.*, d.name AS driver_name 
            FROM shuttles s 
            LEFT JOIN drivers d ON s.driver_id = d.id 
            WHERE s.route LIKE '$like' 
               OR s.shuttle_number LIKE '$like' 
               OR s.from_address LIKE '$like'
               OR s.to_address LIKE '$like'
            ORDER BY s.trip_date ASC, s.depart_time ASC";
    $res = $db->query($sql);
    $trips = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $trips[] = $row;
        }
    }
} else {
    $trips = $tripModel->getAllTrips();
}

include __DIR__ . '/../../../shared_layout/header.php';
include __DIR__ . '/../../../shared_layout/nav.php';
?>

<div class="trips-container">
    <h1>Available Trips</h1>
    
    <!-- Search Form -->
    <div class="search-box">
        <form method="GET" action="">
            <input type="hidden" name="page" value="trips">
            <input type="text" name="q" placeholder="Search by route or shuttle number..." 
                   value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
            <?php if ($search): ?>
                <a href="?page=trips" class="clear-search">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($trips)): ?>
        <p class="no-trips">No trips found.</p>
    <?php else: ?>
        <div class="trips-grid">
            <?php foreach ($trips as $trip): ?>
                <div class="trip-card">
                    <div class="trip-header">
                        <h3><?= htmlspecialchars($trip['shuttle_number']) ?></h3>
                        <span class="trip-price">₱<?= number_format($trip['price'], 2) ?></span>
                    </div>
                    <div class="trip-details">
                        <p><strong>Route:</strong> <?= htmlspecialchars($trip['route'] ?? ($trip['from_address'] . ' to ' . $trip['to_address'])) ?></p>
                        <p><strong>Date:</strong> <?= date('M d, Y', strtotime($trip['trip_date'])) ?></p>
                        <p><strong>Departure:</strong> <?= date('h:i A', strtotime($trip['depart_time'])) ?></p>
                        <p><strong>Arrival:</strong> <?= $trip['arrive_time'] ? date('h:i A', strtotime($trip['arrive_time'])) : 'N/A' ?></p>
                        <p><strong>Driver:</strong> <?= htmlspecialchars($trip['driver_name'] ?? 'Not Assigned') ?></p>
                        <p><strong>Available Seats:</strong> <?= $trip['seats_available'] ?></p>
                    </div>
                    <div class="trip-actions">
                        <a href="?page=trip_detail&id=<?= $trip['id'] ?>" class="btn-view">View Details</a>
                        <a href="?page=seat_management&trip_id=<?= $trip['id'] ?>" class="btn-book">Book Now</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../../shared_layout/footer.php'; ?>
