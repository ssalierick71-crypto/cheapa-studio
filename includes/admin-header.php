<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$cur = basename($_SERVER['PHP_SELF'], '.php');
$group = [
  'index' => 'dashboard',
  'visitors' => 'visitors',
  'orders' => 'orders', 'order-view' => 'orders',
  'leads' => 'leads', 'lead-view' => 'leads',
  'packs' => 'packs', 'pack-edit' => 'packs',
  'products' => 'products', 'product-edit' => 'products',
  'portfolio' => 'portfolio', 'portfolio-edit' => 'portfolio',
  'settings' => 'settings',
][$cur] ?? '';

function asActive(string $g): string { global $group; return $group === $g ? 'active' : ''; }
$newLeadCount  = (int)$pdo->query("SELECT COUNT(*) FROM leads WHERE status='New'")->fetchColumn();
try { $newOrderCount = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='New'")->fetchColumn(); } catch (Throwable $e) { $newOrderCount = 0; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($adminTitle) ? e($adminTitle) . ' — ' : '' ?>Admin · <?= SITE_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body class="admin-body">
<div class="admin-layout">

  <aside class="admin-sidebar">
    <div class="as-brand"><?= brand_logo('dark') ?></div>
    <nav class="as-nav">
      <a href="<?= SITE_URL ?>/admin/index.php"     class="<?= asActive('dashboard') ?>"><i class="bi bi-grid-1x2"></i> <span>Dashboard</span></a>
      <a href="<?= SITE_URL ?>/admin/visitors.php"  class="<?= asActive('visitors') ?>"><i class="bi bi-graph-up-arrow"></i> <span>Visitors</span></a>
      <a href="<?= SITE_URL ?>/admin/orders.php"    class="<?= asActive('orders') ?>"><i class="bi bi-bag-check"></i> <span>Orders<?php if ($newOrderCount): ?> (<?= $newOrderCount ?>)<?php endif; ?></span></a>
      <a href="<?= SITE_URL ?>/admin/leads.php"     class="<?= asActive('leads') ?>"><i class="bi bi-inbox"></i> <span>Leads<?php if ($newLeadCount): ?> (<?= $newLeadCount ?>)<?php endif; ?></span></a>
      <a href="<?= SITE_URL ?>/admin/packs.php"     class="<?= asActive('packs') ?>"><i class="bi bi-box-seam"></i> <span>Packs</span></a>
      <a href="<?= SITE_URL ?>/admin/products.php"  class="<?= asActive('products') ?>"><i class="bi bi-bag"></i> <span>Shop Products</span></a>
      <a href="<?= SITE_URL ?>/admin/portfolio.php" class="<?= asActive('portfolio') ?>"><i class="bi bi-collection"></i> <span>Portfolio</span></a>
      <a href="<?= SITE_URL ?>/admin/settings.php"  class="<?= asActive('settings') ?>"><i class="bi bi-gear"></i> <span>Settings</span></a>
    </nav>
    <div class="as-foot">
      <a href="<?= SITE_URL ?>/" target="_blank" class="btn btn-ghost btn-sm btn-block" style="color:#fff;border-color:rgba(255,255,255,.2);margin-bottom:8px"><i class="bi bi-box-arrow-up-right"></i> View site</a>
      <a href="<?= SITE_URL ?>/admin/logout.php" class="btn btn-primary btn-sm btn-block"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
  </aside>

  <main class="admin-content">
    <div class="admin-topbar">
      <h1><?= isset($adminTitle) ? e($adminTitle) : 'Dashboard' ?></h1>
      <div class="topbar-actions">
        <a href="<?= SITE_URL ?>/" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left"></i> Back to site</a>
        <?php if (!empty($adminAction)) echo $adminAction; ?>
      </div>
    </div>

    <?php if (!empty($_GET['ok'])): ?>
      <div class="flash flash-ok"><i class="bi bi-check-circle-fill"></i> <?= e($_GET['ok']) ?></div>
    <?php endif; ?>
    <?php if (!empty($flashError)): ?>
      <div class="flash flash-err"><i class="bi bi-exclamation-circle-fill"></i> <?= e($flashError) ?></div>
    <?php endif; ?>
