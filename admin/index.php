<?php
$adminTitle = 'Dashboard';
require_once dirname(__DIR__) . '/includes/admin-header.php';

$counts = [
  'orders'    => (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
  'newOrders' => (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='New'")->fetchColumn(),
  'revenue'   => (int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='Completed'")->fetchColumn(),
  'pipeline'  => (int)$pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status NOT IN ('Completed')")->fetchColumn(),
  'leads'    => (int)$pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn(),
  'newLeads' => (int)$pdo->query("SELECT COUNT(*) FROM leads WHERE status='New'")->fetchColumn(),
  'packs'    => (int)$pdo->query("SELECT COUNT(*) FROM packs")->fetchColumn(),
  'products' => (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
  'portfolio'=> (int)$pdo->query("SELECT COUNT(*) FROM portfolio")->fetchColumn(),
];
$orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6")->fetchAll();
$leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 6")->fetchAll();

// visitor snapshot (last 7 days)
$visToday = 0; $visWeekPV = 0; $vseries = []; $vmax = 1;
try {
    $visToday  = (int)$pdo->query("SELECT COUNT(DISTINCT ip_hash) FROM visits WHERE is_bot=0 AND day=" . sql_today())->fetchColumn();
    $visWeekPV = (int)$pdo->query("SELECT COUNT(*) FROM visits WHERE is_bot=0 AND day >= " . sql_days_ago(6))->fetchColumn();
    $vrows = [];
    foreach ($pdo->query("SELECT day, COUNT(DISTINCT ip_hash) uv FROM visits WHERE is_bot=0 AND day >= " . sql_days_ago(6) . " GROUP BY day") as $r) $vrows[$r['day']] = (int)$r['uv'];
    for ($i = 6; $i >= 0; $i--) { $d = date('Y-m-d', strtotime("-$i day")); $vseries[] = ['day' => $d, 'uv' => $vrows[$d] ?? 0]; }
    $vmax = max(1, max(array_column($vseries, 'uv')));
} catch (Throwable $e) {}
?>

<p style="color:var(--text-faint);font-size:13.5px;margin:-12px 0 20px">Welcome back, <strong><?= e($_SESSION['admin_username']) ?></strong>. Here's your business at a glance.</p>

<div class="stat-grid">
  <div class="stat-card"><div class="sc-ico" style="background:#FEF0C7;color:#B54708"><i class="bi bi-bag-check"></i></div><b><?= $counts['newOrders'] ?></b><span>New orders</span></div>
  <div class="stat-card"><div class="sc-ico"><i class="bi bi-bag"></i></div><b><?= $counts['orders'] ?></b><span>Total orders</span></div>
  <div class="stat-card"><div class="sc-ico" style="background:#E0E7FF;color:#3538CD"><i class="bi bi-hourglass-split"></i></div><b style="font-size:1.2rem"><?= ugx($counts['pipeline']) ?></b><span>Pipeline value</span></div>
  <div class="stat-card"><div class="sc-ico" style="background:#D1FADF;color:#027A48"><i class="bi bi-cash-stack"></i></div><b style="font-size:1.2rem"><?= ugx($counts['revenue']) ?></b><span>Completed revenue</span></div>
</div>

<div class="admin-card" style="margin-bottom:24px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <h2 style="font-size:1.15rem"><i class="bi bi-graph-up-arrow" style="color:var(--violet)"></i> Visitors</h2>
    <a href="<?= SITE_URL ?>/admin/visitors.php" class="btn btn-ghost btn-sm">Full report <i class="bi bi-arrow-right"></i></a>
  </div>
  <div style="display:flex;align-items:flex-end;gap:26px;flex-wrap:wrap">
    <div><div style="font-family:'Sora';font-weight:800;font-size:2rem;line-height:1"><?= $visToday ?></div><div style="color:var(--text-faint);font-size:13px">visitors today</div></div>
    <div><div style="font-family:'Sora';font-weight:800;font-size:2rem;line-height:1"><?= number_format($visWeekPV) ?></div><div style="color:var(--text-faint);font-size:13px">page views this week</div></div>
    <div class="vchart vchart-mini" style="flex:1;min-width:200px">
      <?php foreach ($vseries as $s): $hpct = round($s['uv'] / $vmax * 100); ?>
        <div class="vbar-col" title="<?= date('D d M', strtotime($s['day'])) ?>: <?= $s['uv'] ?> visitors">
          <div class="vbar" style="height:<?= max(3,$hpct) ?>%"></div>
          <div class="vbar-day"><?= date('D', strtotime($s['day']))[0] ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php if ($orders): ?>
<div class="admin-card" style="margin-bottom:24px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <h2 style="font-size:1.15rem">Recent Orders</h2>
    <a href="<?= SITE_URL ?>/admin/orders.php" class="btn btn-ghost btn-sm">View all <i class="bi bi-arrow-right"></i></a>
  </div>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Order</th><th>When</th><th>Customer</th><th>Total</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><strong><?= e($o['order_no']) ?></strong></td>
          <td style="white-space:nowrap"><?= date('d M, H:i', strtotime($o['created_at'])) ?></td>
          <td><?= e($o['customer_name'] ?: $o['whatsapp']) ?></td>
          <td><strong><?= ugx($o['total']) ?></strong></td>
          <td><span class="badge" style="<?= status_style($o['status']) ?>"><?= e($o['status']) ?></span></td>
          <td><a href="<?= SITE_URL ?>/admin/order-view.php?id=<?= (int)$o['id'] ?>" class="btn-icon"><i class="bi bi-eye"></i></a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr));margin-bottom:26px">
  <a href="<?= SITE_URL ?>/admin/packs.php" class="admin-card" style="display:flex;align-items:center;gap:12px;text-decoration:none">
    <span class="sc-ico" style="margin:0"><i class="bi bi-box-seam"></i></span><div><b style="font-family:'Sora';font-size:1.3rem"><?= $counts['packs'] ?></b><div style="font-size:13px;color:var(--text-faint)">Packs</div></div>
  </a>
  <a href="<?= SITE_URL ?>/admin/products.php" class="admin-card" style="display:flex;align-items:center;gap:12px;text-decoration:none">
    <span class="sc-ico" style="margin:0"><i class="bi bi-bag"></i></span><div><b style="font-family:'Sora';font-size:1.3rem"><?= $counts['products'] ?></b><div style="font-size:13px;color:var(--text-faint)">Products</div></div>
  </a>
  <a href="<?= SITE_URL ?>/admin/portfolio.php" class="admin-card" style="display:flex;align-items:center;gap:12px;text-decoration:none">
    <span class="sc-ico" style="margin:0"><i class="bi bi-collection"></i></span><div><b style="font-family:'Sora';font-size:1.3rem"><?= $counts['portfolio'] ?></b><div style="font-size:13px;color:var(--text-faint)">Case studies</div></div>
  </a>
</div>

<div class="admin-card">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
    <h2 style="font-size:1.15rem">Recent Leads</h2>
    <a href="<?= SITE_URL ?>/admin/leads.php" class="btn btn-ghost btn-sm">View all <i class="bi bi-arrow-right"></i></a>
  </div>
  <?php if (!$leads): ?>
    <p style="color:var(--text-faint)">No leads yet. Form and chatbot submissions appear here.</p>
  <?php else: ?>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>When</th><th>Name</th><th>WhatsApp</th><th>Service</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($leads as $l): ?>
        <tr>
          <td style="white-space:nowrap"><?= date('d M, H:i', strtotime($l['created_at'])) ?></td>
          <td><?= e($l['name'] ?: '—') ?></td>
          <td><?= e($l['whatsapp']) ?></td>
          <td><?= e($l['service_type'] ?: '—') ?></td>
          <td><span class="badge" style="<?= status_style($l['status']) ?>"><?= e($l['status']) ?></span></td>
          <td><a href="<?= SITE_URL ?>/admin/lead-view.php?id=<?= (int)$l['id'] ?>" class="btn-icon"><i class="bi bi-eye"></i></a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
