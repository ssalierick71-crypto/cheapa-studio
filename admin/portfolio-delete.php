<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT before_image, after_image FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    if ($row = $stmt->fetch()) {
        delete_image('portfolio', $row['before_image']);
        delete_image('portfolio', $row['after_image']);
        $pdo->prepare("DELETE FROM portfolio WHERE id = ?")->execute([$id]);
    }
}
header('Location: ' . SITE_URL . '/admin/portfolio.php?ok=' . rawurlencode('Case study deleted'));
exit;
