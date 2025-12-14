<?php
$passengerName = $_SESSION['passenger_name'] ?? 'Guest';
$passengerId   = $_SESSION['passenger_id'] ?? 'N/A';
$seats         = $seats ?? []; // booked seats only
$ownSeat       = $ownSeat ?? null; // your booked seat
$message       = $message ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seat Management</title>
    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/seat_management.css">
</head>
<body>
    <div class="seat-container">
        <div class="seat-header">
            <a href="index.php?page=dashboard" class="back">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <span>Seat Management</span>
        </div>

        <div class="seat-title">Select your seat</div>

        <?php if (!empty($message)): ?>
            <div style="text-align:center; color:red; font-weight:bold; margin:12px;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="seat-grid">
            <?php for ($i = 1; $i <= ($capacity ?? 12); $i++): ?>
                <?php
                    $isBooked = in_array($i, $seats);
                    $isOwn    = ($ownSeat == $i);
                    $classes  = "seat-btn";

                    if ($isOwn) {
                        $classes .= " own";
                    } elseif ($isBooked) {
                        $classes .= " booked";
                    }
                ?>
                <button class="<?= $classes ?>"
                        onclick="selectSeat(this)"
                        <?= ($isBooked && !$isOwn) ? 'disabled' : '' ?>>
                    <?= $i ?>
                    <?php if ($isOwn): ?>
                        <div style="font-size:0.7em;">Yours</div>
                    <?php endif; ?>
                </button>
            <?php endfor; ?>
        </div>

        <div class="seat-actions">
            <form method="POST" action="index.php?page=seat-confirm&shuttle_id=<?= htmlspecialchars($_GET['shuttle_id'] ?? 1) ?>">
                <input type="hidden" name="selected_seat" id="selectedSeat">
                <button type="submit" name="confirm">Confirm</button>
                <a href="index.php?page=dashboard" class="btn-primary">Cancel</a>
            </form>
        </div>
    </div>

    <script>
        function selectSeat(btn) {
            if (!btn.disabled) {
                document.querySelectorAll('.seat-btn').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                document.getElementById('selectedSeat').value = btn.textContent.trim();
            }
        }
    </script>
</body>
</html>
