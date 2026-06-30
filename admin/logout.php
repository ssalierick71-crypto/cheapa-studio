<?php
require_once dirname(__DIR__) . '/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$_SESSION = [];
session_destroy();
header('Location: ' . SITE_URL . '/admin/login.php');
exit;
