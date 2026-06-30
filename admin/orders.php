<?php
require_once dirname(__DIR__) . '/config.php';
$adminTitle = 'Orders';
require_once dirname(__DIR__) . '/includes/admin-header.php';

$filter = trim($_GET['status'] ?? '');
$statuses = lead_statuses();
if ($filter && in_array($filter, $statuses, true)) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
    $orders = $stmt->fetchAll();
} else {
    $orders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC")->fetchAll();
}
?>

<div class="stage-selector" style="justify-content:flex-start;margin-bottom:20px">
  <a href="<?= SITE_URL ?>/admin/orders.php" class="stage-chip <?= $filter===''?'active':'' ?>" style="text-decoration:none">All</a>
  <?php foreach ($statuses as $s): ?>
    <a href="?status=<?= rawurlencode($s) ?>" class="stage-chip <?= $filter===$s?'active':'' ?>" style="text-decoration:none"><?= e($s) ?></a>
  <?php endforeach; ?>
</div>

<div class="admin-card">
  <?php if (!$orders): ?>
    <p style="color:var(--text-faint)">No orders<?= $filter ? ' with status “'.e($filter).'”' : '' ?> yet. Orders placed in-app or via WhatsApp checkout appear here.</p>
  <?php else: ?>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Order</th><th>When</th><th>Customer</th><th>WhatsApp</th><th>Total</th><th>Deposit</th><th>Channel</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
          <td><strong><?= e($o['order_no']) ?></strong></td>
          <td style="white-space:nowrap"><?= date('d M, H:i', strtotime($o['created_at'])) ?></td>
          <td><?= e($o['customer_name'] ?: $o['business_name'] ?: '—') ?><?php if ($o['customer_name'] && $o['business_name']): ?><br><span style="color:var(--text-faint);font-size:12px"><?= e($o['business_name']) ?></span><?php endif; ?></td>
          <td><?= e($o['whatsapp']) ?></td>
          <td><strong><?= ugx($o['total']) ?></strong></td>
          <td><?= ugx($o['deposit']) ?></td>
          <td><span class="badge"><?= $o['channel']==='whatsapp' ? 'WhatsApp' : 'In-app' ?></span></td>
          <td><span class="badge" style="<?= status_style($o['status']) ?>"><?= e($o['status']) ?></span></td>
          <td>
            <div class="table-actions">
              <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $o['whatsapp'])) ?>" target="_blank" rel="noopener" class="btn-icon" title="WhatsApp" style="color:var(--wa)"><i class="bi bi-whatsapp"></i></a>
              <a href="<?= SITE_URL ?>/admin/order-view.php?id=<?= (int)$o['id'] ?>" class="btn-icon" title="View"><i class="bi bi-eye"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
