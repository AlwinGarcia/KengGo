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
$trips = $tripModel->getAllTrips();

include __DIR__ . '/../../../shared_layout/header.php';
include __DIR__ . '/../../../shared_layout/nav.php';
?>

<link rel="stylesheet" href="app/passenger/view/css/trip_status.css">

<div class="content">
    <h1>Trip Status</h1>

    <?php if (empty($trips)): ?>
        <p>No trips available.</p>
    <?php else: ?>
        <table class="trip-status-table">
            <thead>
            <tr>
                <th>Shuttle #</th>
                <th>Route</th>
                <th>Date</th>
                <th>Departure</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($trips as $trip): ?>
                <tr>
                    <td><?php echo htmlspecialchars($trip['shuttle_number']); ?></td>
                    <td><?php echo htmlspecialchars($trip['route']); ?></td>
                    <td><?php echo htmlspecialchars($trip['trip_date']); ?></td>
                    <td><?php echo htmlspecialchars($trip['departure_time']); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($trip['status'])); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../../shared_layout/footer.php'; ?>
