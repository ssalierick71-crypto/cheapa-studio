<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Track Order';
$order = null; $items = []; $err = '';
$no  = trim($_GET['no'] ?? $_POST['no'] ?? '');
$wa  = trim($_POST['whatsapp'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($no === '' || $wa === '') {
        $err = 'Enter your order number and the WhatsApp number you used.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_no = ? LIMIT 1");
        $stmt->execute([$no]);
        $o = $stmt->fetch();
        if ($o && preg_replace('/\D/', '', $o['whatsapp']) === preg_replace('/\D/', '', $wa)) {
            $order = $o;
            $st = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $st->execute([$o['id']]);
            $items = $st->fetchAll();
        } else {
            $err = "We couldn't find an order with that number and WhatsApp number.";
        }
    }
}
$statuses = lead_statuses();
include 'includes/header.php';
?>

<section class="section">
  <div class="container" style="max-width:620px">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-search"></i> Track Order</span>
      <h1 class="section-title" style="font-size:1.7rem">Where's my order?</h1>
      <p class="section-sub">Enter your order number and WhatsApp number to see its status.</p>
    </div>

    <div class="form-card" style="margin-top:22px">
      <?php if ($err): ?><div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= e($err) ?></div><?php endif; ?>
      <form method="post">
        <div class="form-row">
          <div class="form-group"><label>Order number</label><input name="no" class="form-control" value="<?= e($no) ?>" placeholder="CS260628-0001" required></div>
          <div class="form-group"><label>WhatsApp number</label><input name="whatsapp" class="form-control" value="<?= e($wa) ?>" placeholder="0753 168599" required></div>
        </div>
        <button class="btn btn-primary btn-block"><i class="bi bi-search"></i> Track Order</button>
      </form>
    </div>

    <?php if ($order): ?>
    <div class="admin-card" style="margin-top:18px">
      <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px">
        <div><div style="font-family:'Sora';font-weight:800;font-size:1.1rem"><?= e($order['order_no']) ?></div><div style="color:var(--text-faint);font-size:13px">Placed <?= date('d M Y', strtotime($order['created_at'])) ?></div></div>
        <span class="badge" style="<?= status_style($order['status']) ?>;font-size:13px;padding:6px 14px"><?= e($order['status']) ?></span>
      </div>

      <div style="display:flex;flex-direction:column;gap:8px;margin:18px 0">
        <?php $curIdx = array_search($order['status'], $statuses, true); foreach ($statuses as $i => $s): $done = $i <= $curIdx; ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:13.5px;<?= $done ? '' : 'opacity:.45' ?>">
          <span style="width:22px;height:22px;border-radius:50%;display:grid;place-items:center;font-size:12px;<?= $done ? 'background:var(--violet);color:#fff' : 'background:var(--bg-tint);color:var(--text-faint)' ?>"><i class="bi <?= $done ? 'bi-check' : 'bi-circle' ?>"></i></span>
          <strong><?= e($s) ?></strong>
        </div>
        <?php endforeach; ?>
      </div>

      <?php foreach ($items as $it): ?>
      <div class="co-item"><div class="co-item-main"><div class="co-item-name"><?= e($it['name']) ?><?= $it['variant'] ? ' · '.e($it['variant']) : '' ?></div><div class="co-item-sub"><?= (int)$it['qty'] ?> <?= e($it['unit_label']) ?></div></div><div class="co-item-total"><?= ugx($it['line_total']) ?></div></div>
      <?php endforeach; ?>
      <div class="co-line co-total"><span>Total</span><strong><?= ugx($order['total']) ?></strong></div>
      <div class="co-line co-deposit"><span>Deposit</span><strong><?= ugx($order['deposit']) ?></strong></div>

      <a href="<?= wa_link('Hello Cheapa Studio! About my order '.$order['order_no'].'…') ?>" target="_blank" rel="noopener" class="btn btn-wa btn-block" style="margin-top:14px"><i class="bi bi-whatsapp"></i> Message us about this order</a>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
