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

// optional search
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($search !== '') {
    $like = '%' . $db->real_escape_string($search) . '%';
    $sql = "SELECT s.*, d.name AS driver_name
            FROM shuttles s
            LEFT JOIN drivers d ON s.driver_id = d.id
            WHERE s.route LIKE '$like'
               OR s.shuttle_number LIKE '$like'
            ORDER BY s.trip_date ASC, s.departure_time ASC";
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

<link rel="stylesheet" href="app/passenger/view/css/dashboard.css">

<div class="content">
    <h1>Available Trips</h1>

    <form method="get" class="search-form">
        <input type="hidden" name="page" value="trips">
        <input type="text" name="q"
               placeholder="Search by route or shuttle number"
               value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (empty($trips)): ?>
        <p>No trips found.</p>
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
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($trips as $trip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($trip['shuttle_number']); ?></td>
                    <td><?php echo htmlspecialchars($trip['route']); ?></td>
                    <td><?php echo htmlspecialchars($trip['trip_date']); ?></td>
                    <td><?php echo htmlspecialchars($trip['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars($trip['arrival_time']); ?></td>
                    <td><?php echo number_format($trip['price'], 2); ?></td>
                    <td><?php echo htmlspecialchars($trip['driver_name'] ?? ''); ?></td>
                    <td>
                        <a href="index.php?page=trip_detail&id=<?php echo $trip['id']; ?>">
                            View
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../../shared_layout/footer.php'; ?>
