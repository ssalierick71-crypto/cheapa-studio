<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT image FROM packs WHERE id = ?");
    $stmt->execute([$id]);
    if ($row = $stmt->fetch()) {
        delete_image('packs', $row['image']);
        $pdo->prepare("DELETE FROM packs WHERE id = ?")->execute([$id]);
    }
}
header('Location: ' . SITE_URL . '/admin/packs.php?ok=' . rawurlencode('Pack deleted'));
exit;
