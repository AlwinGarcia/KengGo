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
$passengerId = $session->get('user_id');

// Get all active trips and the passenger's bookings
$query = "SELECT s.*, d.name AS driver_name, b.id AS booking_id, b.seat_number
          FROM shuttles s
          LEFT JOIN drivers d ON s.driver_id = d.id
          LEFT JOIN bookings b ON s.id = b.shuttle_id AND b.passenger_id = ? AND b.status = 'booked'
          WHERE s.status IN ('active', 'pending', 'completed')
          ORDER BY s.trip_date ASC, s.depart_time ASC";

$stmt = $db->prepare($query);
$stmt->bind_param("i", $passengerId);
$stmt->execute();
$result = $stmt->get_result();

$trips = [];
while ($row = $result->fetch_assoc()) {
    $trips[] = $row;
}

// Function to determine trip status based on time
function getTripStatus($trip) {
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $tripDate = $trip['trip_date'];
    $departTime = $trip['depart_time'];
    $arriveTime = $trip['arrive_time'];
    
    if ($trip['status'] === 'cancelled') {
        return 'Cancelled';
    }
    
    if ($tripDate < $currentDate) {
        return 'Completed';
    }
    
    if ($tripDate == $currentDate) {
        if ($arriveTime && $currentTime >= $arriveTime) {
            return 'Arrived';
        } elseif ($currentTime >= $departTime) {
            return 'In Transit';
        } elseif ($currentTime >= date('H:i:s', strtotime($departTime . ' -15 minutes'))) {
            return 'Boarding';
        } else {
            return 'On Time';
        }
    }
    
    return 'Scheduled';
}

include __DIR__ . '/../../../shared_layout/header.php';
include __DIR__ . '/../../../shared_layout/nav.php';
?>

<div class="trip-status-container">
    <h1>Trip Status</h1>
    
    <div class="status-filter">
        <button class="filter-btn active" data-filter="all">All Trips</button>
        <button class="filter-btn" data-filter="booked">My Bookings</button>
        <button class="filter-btn" data-filter="active">Active</button>
    </div>

    <?php if (empty($trips)): ?>
        <p class="no-trips">No trips available.</p>
    <?php else: ?>
        <div class="status-grid">
            <?php foreach ($trips as $trip): 
                $status = getTripStatus($trip);
                $isBooked = !empty($trip['booking_id']);
                $statusClass = strtolower(str_replace(' ', '-', $status));
            ?>
                <div class="status-card <?= $isBooked ? 'booked' : '' ?>" data-status="<?= $statusClass ?>">
                    <div class="card-header">
                        <div class="shuttle-info">
                            <h3><?= htmlspecialchars($trip['shuttle_number']) ?></h3>
                            <?php if ($isBooked): ?>
                                <span class="booked-badge">✓ Booked (Seat <?= $trip['seat_number'] ?>)</span>
                            <?php endif; ?>
                        </div>
                        <span class="status-indicator status-<?= $statusClass ?>">
                            <?= $status ?>
                        </span>
                    </div>
                    
                    <div class="route-info">
                        <div class="route-from">
                            <span class="icon">📍</span>
                            <div>
                                <p class="label">From</p>
                                <p class="address"><?= htmlspecialchars($trip['from_address']) ?></p>
                            </div>
                        </div>
                        <div class="route-arrow">→</div>
                        <div class="route-to">
                            <span class="icon">📍</span>
                            <div>
                                <p class="label">To</p>
                                <p class="address"><?= htmlspecialchars($trip['to_address']) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="trip-info">
                        <div class="info-item">
                            <span class="icon">📅</span>
                            <span><?= date('M d, Y', strtotime($trip['trip_date'])) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="icon">🕐</span>
                            <span><?= date('h:i A', strtotime($trip['depart_time'])) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="icon">👤</span>
                            <span><?= htmlspecialchars($trip['driver_name'] ?? 'TBA') ?></span>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <a href="?page=trip_detail&id=<?= $trip['id'] ?>" class="btn-details">View Details</a>
                        <?php if (!$isBooked && $trip['seats_available'] > 0 && $trip['status'] === 'active'): ?>
                            <a href="?page=seat_management&trip_id=<?= $trip['id'] ?>" class="btn-book">Book</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Filter functionality
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.dataset.filter;
        document.querySelectorAll('.status-card').forEach(card => {
            if (filter === 'all') {
                card.style.display = 'block';
            } else if (filter === 'booked') {
                card.style.display = card.classList.contains('booked') ? 'block' : 'none';
            } else if (filter === 'active') {
                const status = card.dataset.status;
                card.style.display = ['on-time', 'boarding', 'in-transit', 'scheduled'].includes(status) ? 'block' : 'none';
            }
        });
    });
});

// Auto-refresh every 30 seconds
setTimeout(() => location.reload(), 30000);
</script>

<?php include __DIR__ . '/../../../shared_layout/footer.php'; ?>
