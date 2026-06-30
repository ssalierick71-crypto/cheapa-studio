<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_id'])) {
    header('Location: ' . (defined('SITE_URL') ? SITE_URL : '') . '/admin/login.php');
    exit;
}
