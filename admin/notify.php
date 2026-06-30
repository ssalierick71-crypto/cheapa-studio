<?php
// Lightweight JSON endpoint the admin dashboard polls for new orders.
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'auth']);
    exit;
}

$latest = (int)$pdo->query("SELECT COALESCE(MAX(id),0) FROM orders")->fetchColumn();
$new    = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='New'")->fetchColumn();
echo json_encode(['latest' => $latest, 'new' => $new]);
