<?php
$passengerName = $_SESSION['passenger_name'] ?? 'Guest';
$passengerId   = $_SESSION['passenger_id'] ?? 'N/A';
$seats         = $seats ?? []; // booked seats only
$message       = $message ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seat Management</title>
    <link rel="stylesheet" href="/KengGo/app/passenger/view/css/seat_management.css">
    <style>
        .seat-btn {
            width: 50px;
            height: 50px;
            margin: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            background-color: #f0f0f0; /* default available */
            cursor: pointer;
        }
        .seat-btn.selected {
            background-color: #4CAF50; /* green for selected */
            color: #fff;
        }
        .seat-btn.booked {
            background-color: #ff4d4d; /* red for booked */
            color: #fff;
            cursor: not-allowed;
        }
    </style>
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
            <?php for ($i = 1; $i <= 12; $i++): ?>
                <?php $isBooked = in_array($i, $seats); ?>
                <button class="seat-btn <?= $isBooked ? 'booked' : '' ?>"
                        onclick="selectSeat(this)"
                        <?= $isBooked ? 'disabled' : '' ?>>
                    <?= $i ?>
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
                document.getElementById('selectedSeat').value = btn.textContent;
            }
        }
    </script>
</body>
</html>
