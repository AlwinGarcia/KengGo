<?php
/**
 * Password Setup Script (KengGo)
 * One-time tool to ensure all user passwords are stored as secure hashes.
 *
 * Access via: http://localhost/KengGo/handlers/setup_passwords.php
 * After running and confirming, DELETE this file in production.
 */

require_once __DIR__ . '/../includes/db_connect.php';

function is_hashed(string $value): bool
{
    if ($value === '') {
        return false;
    }
    $info = password_get_info($value);
    return ($info['algo'] !== 0);
}

function update_passwords(mysqli $conn, string $table, string $idCol, string $passwordCol, ?callable $usernameFormatter = null): array
{
    $updated = 0;
    $errors = [];
    $rows = [];

    $result = $conn->query("SELECT {$conn->real_escape_string($idCol)} AS id, {$conn->real_escape_string($passwordCol)} AS pwd FROM {$conn->real_escape_string($table)}");
    if (!$result) {
        return [
            'updated' => 0,
            'errors' => ["Failed to fetch from {$table}: " . $conn->error],
            'rows' => []
        ];
    }

    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $result->close();

    $stmt = $conn->prepare("UPDATE {$table} SET {$passwordCol} = ? WHERE {$idCol} = ?");
    if (!$stmt) {
        return [
            'updated' => 0,
            'errors' => ["Failed to prepare update for {$table}: " . $conn->error],
            'rows' => []
        ];
    }

    foreach ($rows as $r) {
        $id = (int)$r['id'];
        $pwd = (string)$r['pwd'];

        if (is_hashed($pwd)) {
            continue; // already hashed
        }

        try {
            $hash = password_hash($pwd, PASSWORD_BCRYPT);
            $stmt->bind_param('si', $hash, $id);
            if ($stmt->execute()) {
                $updated++;
            } else {
                $errors[] = "Failed to update ID {$id} in {$table}: " . $stmt->error;
            }
        } catch (Throwable $e) {
            $errors[] = "Error hashing ID {$id} in {$table}: " . $e->getMessage();
        }
    }

    $stmt->close();

    return [
        'updated' => $updated,
        'errors' => $errors,
        'rows' => $rows
    ];
}

// Process tables used in this project
$summary = [];
$allErrors = [];

$summary['admins'] = update_passwords($conn, 'admins', 'id', 'password');
$allErrors = array_merge($allErrors, $summary['admins']['errors']);

$summary['drivers'] = update_passwords($conn, 'drivers', 'id', 'password');
$allErrors = array_merge($allErrors, $summary['drivers']['errors']);

$summary['passengers'] = update_passwords($conn, 'passengers', 'id', 'password');
$allErrors = array_merge($allErrors, $summary['passengers']['errors']);

$totalUpdated = ($summary['admins']['updated'] ?? 0) + ($summary['drivers']['updated'] ?? 0) + ($summary['passengers']['updated'] ?? 0);

// Render HTML response
echo "<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>KengGo Password Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 40px auto; padding: 0 20px; background: #f7f7f9; }
        .container { background: #fff; padding: 24px 28px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        h1 { color: #333; margin: 0 0 10px; }
        .sub { color: #666; margin: 0 0 20px; }
        .success { color: #155724; background: #d4edda; padding: 10px 12px; border-radius: 6px; margin: 10px 0; }
        .error { color: #721c24; background: #f8d7da; padding: 10px 12px; border-radius: 6px; margin: 10px 0; }
        .info { color: #0c5460; background: #d1ecf1; padding: 12px 14px; border-radius: 6px; margin: 16px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #4CAF50; color: white; }
        .muted { color: #888; }
        .badge { display: inline-block; padding: 3px 8px; background: #eee; border-radius: 12px; font-size: 12px; }
        .mt-2 { margin-top: 12px; }
        .mt-3 { margin-top: 18px; }
        .mt-4 { margin-top: 24px; }
    </style>
    <meta name='robots' content='noindex, nofollow'>
</head>
<body>
  <div class='container'>
    <h1>🔐 KengGo Password Setup</h1>
    <p class='sub'>This script hashes any remaining plaintext passwords for admins, drivers, and passengers using bcrypt.</p>";

echo "<h2 class='mt-3'>Processing Summary</h2>";

foreach (['admins' => 'Admins', 'drivers' => 'Drivers', 'passengers' => 'Passengers'] as $key => $label) {
    $upd = $summary[$key]['updated'] ?? 0;
    if ($upd > 0) {
        echo "<div class='success'>✓ {$label}: updated {$upd} password(s) to bcrypt.</div>";
    } else {
        echo "<div class='info'>ℹ {$label}: no updates needed.</div>";
    }
}

echo "<div class='mt-3'><strong>Total Updated:</strong> {$totalUpdated}</div>";

if (!empty($allErrors)) {
    echo "<div class='mt-3 error'><strong>Errors (" . count($allErrors) . "):</strong><ul>";
    foreach ($allErrors as $err) {
        echo "<li>" . htmlspecialchars($err, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</li>";
    }
    echo "</ul></div>";
} else {
    echo "<div class='mt-3 success'>✓ Completed without errors.</div>";
}

echo "<div class='info mt-4'>
    <p><strong>Security Note:</strong></p>
    <ul>
        <li>Delete this setup file after running it (handlers/setup_passwords.php).</li>
        <li>Ensure registration and login use password hashing (bcrypt) with password_verify().</li>
        <li>Change default/test passwords for production users.</li>
    </ul>
  </div>";

echo "  </div></body></html>";

// Close DB
$conn->close();
?>
