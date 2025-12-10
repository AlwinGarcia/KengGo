<?php
// handlers/admin_notifications.php - Admin notifications API
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db_connect.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$adminId = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : null;

try {
    switch ($action) {
        case 'list':
        default:
            // Check if admin_notifications table exists, create if not
            $tableCheck = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
            if (!$tableCheck || $tableCheck->num_rows == 0) {
                // Create the table
                $createTable = "CREATE TABLE IF NOT EXISTS admin_notifications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    admin_id INT DEFAULT NULL,
                    title VARCHAR(255) NOT NULL,
                    message TEXT NOT NULL,
                    type ENUM('info', 'success', 'warning', 'error', 'alert') DEFAULT 'info',
                    status ENUM('unread', 'read') DEFAULT 'unread',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    read_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                $conn->query($createTable);
                
                // Insert sample notifications
                $conn->query("INSERT INTO admin_notifications (admin_id, title, message, type, status) VALUES
                    (NULL, 'Welcome Admin', 'Welcome to KengGo Shuttle Admin Dashboard', 'info', 'unread'),
                    (NULL, 'System Ready', 'All systems are operational', 'success', 'unread')");
            }
            
            // Fetch notifications for admin
            $sql = "SELECT id, admin_id, title, message, type, status, created_at, read_at 
                    FROM admin_notifications";
            if (!empty($adminId)) {
                $sql .= " WHERE admin_id = ? OR admin_id IS NULL";
            }
            $sql .= " ORDER BY created_at DESC LIMIT 50";

            $stmt = $conn->prepare($sql);
            if (!empty($adminId)) {
                $stmt->bind_param('i', $adminId);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $stmt->close();

            echo json_encode(['success' => true, 'data' => $rows]);
            break;
    }
} catch (Throwable $e) {
    error_log('Admin notifications error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
