<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) { header('Location: ' . SITE_URL . '/admin/orders.php'); exit; }

$flashError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $flashError = 'Security check failed.';
    } elseif (($_POST['action'] ?? '') === 'status') {
        $new = $_POST['status'] ?? '';
        if (in_array($new, lead_statuses(), true)) {
            $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new, $id]);
            header('Location: ' . SITE_URL . '/admin/order-view.php?id=' . $id . '&ok=' . rawurlencode('Status updated to ' . $new));
            exit;
        }
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$id]);
        header('Location: ' . SITE_URL . '/admin/orders.php?ok=' . rawurlencode('Order deleted'));
        exit;
    }
}

$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemsStmt->execute([$id]);
$items = $itemsStmt->fetchAll();
$statuses = lead_statuses();
$deliveryLabel = ['pickup'=>'Free pickup','delivery_kampala'=>'Delivery within Kampala (free)','delivery_far'=>'Outside Kampala (fee TBC on WhatsApp)'][$order['delivery_method']] ?? '—';

$adminTitle  = 'Order ' . $order['order_no'];
$adminAction = '<a href="' . SITE_URL . '/admin/orders.php" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left"></i> Back to orders</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';
?>

<div class="grid" style="grid-template-columns:1.6fr 1fr;gap:20px;align-items:start">

  <div class="admin-card">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:14px">
      <h2 style="font-size:1.15rem"><?= e($order['order_no']) ?></h2>
      <span class="badge"><?= $order['channel']==='whatsapp' ? 'WhatsApp order' : 'In-app order' ?></span>
    </div>
    <table style="width:100%;font-size:14px;margin-bottom:14px">
      <tr><td style="color:var(--text-faint);padding:6px 0;width:130px">Customer</td><td><?= e($order['customer_name'] ?: '—') ?></td></tr>
      <?php if (!empty($order['business_name'])): ?><tr><td style="color:var(--text-faint);padding:6px 0">Business</td><td><?= e($order['business_name']) ?></td></tr><?php endif; ?>
      <tr><td style="color:var(--text-faint);padding:6px 0">WhatsApp</td><td><a href="https://wa.me/<?= e(preg_replace('/\D/', '', $order['whatsapp'])) ?>" target="_blank" rel="noopener" style="color:var(--violet-dk);font-weight:700"><?= e($order['whatsapp']) ?> <i class="bi bi-whatsapp"></i></a></td></tr>
      <?php if ($order['email']): ?><tr><td style="color:var(--text-faint);padding:6px 0">Email</td><td><?= e($order['email']) ?></td></tr><?php endif; ?>
      <tr><td style="color:var(--text-faint);padding:6px 0">Delivery</td><td><?= e($deliveryLabel) ?><?= $order['delivery_address'] ? ' — '.e($order['delivery_address']) : '' ?></td></tr>
      <tr><td style="color:var(--text-faint);padding:6px 0">Payment</td><td><?= e($order['payment_method']) ?></td></tr>
      <tr><td style="color:var(--text-faint);padding:6px 0">Placed</td><td><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></td></tr>
    </table>

    <?php foreach ($items as $it): ?>
    <div class="co-item">
      <div class="co-item-main"><div class="co-item-name"><?= e($it['name']) ?><?= $it['variant'] ? ' · '.e($it['variant']) : '' ?></div>
        <div class="co-item-sub"><?= (int)$it['qty'] ?> <?= e($it['unit_label']) ?> × <?= ugx($it['unit_price']) ?><?= $it['design'] ? ' + design '.ugx($it['design_fee']) : '' ?></div></div>
      <div class="co-item-total"><?= ugx($it['line_total']) ?></div>
    </div>
    <?php endforeach; ?>
    <div class="co-line"><span>Items</span><strong><?= ugx($order['subtotal']) ?></strong></div>
    <?php if ($order['design_total'] > 0): ?><div class="co-line"><span>Design</span><strong><?= ugx($order['design_total']) ?></strong></div><?php endif; ?>
    <div class="co-line co-total"><span>Total</span><strong><?= ugx($order['total']) ?></strong></div>
    <div class="co-line co-deposit"><span>Deposit (50%)</span><strong><?= ugx($order['deposit']) ?></strong></div>

    <?php if (trim($order['notes'])): ?><div style="margin-top:14px;padding:12px;background:var(--bg-soft);border-radius:10px;font-size:14px;white-space:pre-wrap"><strong>Notes:</strong> <?= e($order['notes']) ?></div><?php endif; ?>

    <form method="post" style="margin-top:16px" onsubmit="return confirm('Delete this order permanently?')">
      <?= csrf_field() ?><input type="hidden" name="action" value="delete">
      <button class="btn btn-ghost btn-sm" style="color:var(--red);border-color:#FECDCA"><i class="bi bi-trash"></i> Delete order</button>
    </form>
  </div>

  <div class="admin-card">
    <h3 style="font-size:1rem;margin-bottom:14px">Order workflow</h3>
    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px">
      <?php $curIdx = array_search($order['status'], $statuses, true); foreach ($statuses as $i => $s): $done = $i <= $curIdx; ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:13.5px;<?= $done?'':'opacity:.45' ?>">
          <span style="width:22px;height:22px;border-radius:50%;display:grid;place-items:center;font-size:12px;<?= $done?'background:var(--violet);color:#fff':'background:var(--bg-tint);color:var(--text-faint)' ?>"><i class="bi <?= $done?'bi-check':'bi-circle' ?>"></i></span>
          <strong><?= e($s) ?></strong>
        </div>
      <?php endforeach; ?>
    </div>
    <form method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="status">
      <label style="font-weight:600;font-size:13.5px;display:block;margin-bottom:6px">Set status</label>
      <select name="status" class="form-control" style="margin-bottom:12px">
        <?php foreach ($statuses as $s): ?><option <?= $order['status']===$s?'selected':'' ?>><?= e($s) ?></option><?php endforeach; ?>
      </select>
      <button class="btn btn-primary btn-block btn-sm"><i class="bi bi-check2"></i> Update status</button>
    </form>
  </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
