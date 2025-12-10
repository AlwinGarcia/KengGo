<?php
// dashboard.php - Admin Dashboard with dynamic trip data
require_once __DIR__ . '/../../../includes/db_connect.php';
require_once __DIR__ . '/../../model/Trip.php';

// Get all trips from database
$trip = new Trip($conn);
$trips = $trip->getAllTrips();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Dashboard — KengGo Shuttle</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css" />
</head>
<body class="dashboard-page">
<main class="dashboard-shell">

    <!-- status bar -->
    <div class="dashboard-topbar">
        <div class="top-left">
            <span class="status-time">9:41</span>
        </div>
        <div class="top-right">
            <span class="status-icons">📶 🔋</span>
        </div>
    </div>

    <!-- header -->
    <section class="dashboard-header">
        <div class="avatar">🟩</div>
        <div class="greeting">
            <div class="greeting-line">Hey, <strong>Gideon</strong></div>
        </div>
        <button class="icon-btn bell" aria-label="Notifications">🔔</button>
    </section>

    <!-- welcome card -->
    <section class="welcome-card" aria-label="Welcome">
        <div class="welcome-left">
            <h2>Welcome Admin</h2>
            <div class="admin-id">
                <span class="meta">Admin ID</span>
                <div class="id-value">1*** 9***</div>
            </div>
        </div>
        <button class="card-more" aria-label="More options" onclick="showWelcomeMenu(event)">⋯</button>
    </section>

    <!-- Trips section -->
    <section class="trips-section">
        <div class="trips-header">
            <h3>Trips Ongoing</h3>
            <div class="trips-actions">
                <button class="fab small" title="Delete" onclick="window.location.href='deleteTrip.html'">🗑️</button>
                <button class="fab small" title="Add" onclick="window.location.href='addTrip.html'">➕</button>
            </div>
        </div>

        <!-- cards grid -->
        <div class="trips-grid">
            <?php if (empty($trips)): ?>
                <p style="text-align: center; padding: 2rem; grid-column: 1/-1; color: #666;">No trips available. Add a new trip to get started!</p>
            <?php else: ?>
                <?php foreach ($trips as $tripData): 
                    // Parse route into from and to
                    $routeParts = explode(' to ', $tripData['route']);
                    $from = isset($routeParts[0]) ? $routeParts[0] : 'N/A';
                    $to = isset($routeParts[1]) ? $routeParts[1] : 'N/A';
                    $shuttleNumber = htmlspecialchars($tripData['shuttle_number'] ?? 'Shuttle ' . $tripData['id']);
                    $price = number_format($tripData['price'] ?? 0, 2);
                    $tripId = $tripData['id'];
                ?>
                <article class="trip-card" onclick="editTrip(<?php echo $tripId; ?>)">
                    <div class="trip-card-top">
                        <div class="icon">🚌</div>
                        <div class="trip-title"><?php echo $shuttleNumber; ?></div>
                        <div class="status-dot <?php echo strtolower($tripData['status'] ?? 'active'); ?>"></div>
                    </div>

                    <div class="trip-body">
                        <div class="route">
                            <div class="from">↙ From: <?php echo htmlspecialchars($from); ?></div>
                            <div class="to">↗ To: <?php echo htmlspecialchars($to); ?></div>
                        </div>

                        <div class="price">
                            <span>Price:</span>
                            <strong>₱<?php echo $price; ?></strong>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Toast notification -->
    <div class="toast" id="toast"></div>

    <!-- bottom navigation -->
    <nav class="bottom-nav" role="navigation" aria-label="Main navigation">
        <button class="nav-item nav-item--active" onclick="navToPage('dashboard.php')" aria-label="Dashboard">🏠</button>
        <button class="nav-item" onclick="navToPage('addTrip.html')" aria-label="Add">➕</button>
        <button class="nav-item" onclick="navToPage('profile.html')" aria-label="Profile">👤</button>
        <button class="nav-item" onclick="navToPage('notifications.html')" aria-label="Notifications">🔔</button>
        <button class="nav-item" onclick="navToPage('trip_status.html')" aria-label="Status">📊</button>
    </nav>

</main>

<script>
    // Toast notification helper
    function showToast(message, type = 'info', duration = 3000) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `toast show ${type}`;
        setTimeout(() => {
            toast.classList.remove('show');
        }, duration);
    }

    // Edit trip
    function editTrip(tripId) {
        window.location.href = 'editTrip.html?id=' + tripId;
    }

    // Navigation
    function navToPage(page) {
        window.location.href = page;
    }

    // Welcome menu
    function showWelcomeMenu(e) {
        e.stopPropagation();
        alert('Menu options coming soon!');
    }

    // Update time
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const timeEl = document.querySelector('.status-time');
        if (timeEl) {
            timeEl.textContent = hours + ':' + minutes;
        }
    }

    updateTime();
    setInterval(updateTime, 60000);
</script>

</body>
</html>