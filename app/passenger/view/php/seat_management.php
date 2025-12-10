<?php
$passengerName = $_SESSION['passenger_name'] ?? 'Guest';
$passengerId   = $_SESSION['passenger_id'] ?? 'N/A';
$seats         = $seats ?? [];
$message       = $message ?? null; // message from controller
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Management</title>
    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/seat_management.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="seat-container">
        <div class="seat-header">
            <!--  Back button  -->
            <a href="index.php?page=dashboard" class="back">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Seat Management</span>
        </div>

        <div class="seat-title">Select your seat</div>

        <!-- Message area -->
        <?php if (!empty($message)): ?>
            <div style="text-align:center; color:red; font-weight:bold; margin:12px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="seat-grid">
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <?php $isBooked = in_array($i, $seats); ?>
                <button class="seat-btn <?= $isBooked ? '' : '' ?>"
                        onclick="selectSeat(this)"
                        <?= $isBooked ? 'disabled' : '' ?>>
                    <?= $i ?>
                </button>
            <?php endfor; ?>
        </div>

        <div class="seat-actions">
            <form method="POST" action="index.php?page=seat-management&shuttle_id=<?= htmlspecialchars($_GET['shuttle_id'] ?? 1) ?>">
                <input type="hidden" name="selected_seat" id="selectedSeat">
                <button type="submit" name="confirm">Confirm</button>
                <button type="submit" name="cancel">Cancel</button>
            </form>
        </div>

        <div class="bottom-nav">
            <span class="nav-icon"><i class="fa-solid fa-house"></i></span>
            <span class="nav-icon"><i class="fa-regular fa-location-dot"></i></span>
            <span class="nav-icon"><i class="fa-solid fa-clipboard-list"></i></span>
            <span class="nav-icon"><i class="fa-solid fa-user"></i></span>
            <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
        </div>
    </div>

    <script>
        // Seat selection logic
        function selectSeat(btn) {
            if (!btn.disabled) {
                // remove previous selection
                document.querySelectorAll('.seat-btn').forEach(b => b.classList.remove('selected'));
                // mark new selection
                btn.classList.add('selected');
                // set hidden input value
                document.getElementById('selectedSeat').value = btn.textContent;
            }
        }
    </script>
</body>
</html>
