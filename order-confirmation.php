<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$orderId = (int)($_SESSION['order_done'] ?? 0);
if (!$orderId) { header('Location: ' . SITE_URL . '/shop.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$orderId]);
$order = $stmt->fetch();
if (!$order) { header('Location: ' . SITE_URL . '/shop.php'); exit; }

$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

$waUrl = $_SESSION['order_wa'] ?? '';
unset($_SESSION['order_wa']); // one-time

$deliveryLabel = [
  'pickup' => 'Free pickup at our location',
  'delivery_kampala' => 'Delivery within Kampala (free)',
  'delivery_far' => 'Delivery outside Kampala (fee confirmed on WhatsApp)',
][$order['delivery_method']] ?? 'Pickup';

$pageTitle = 'Order ' . $order['order_no'];
include 'includes/header.php';
?>

<section class="section">
  <div class="container" style="max-width:680px">
    <div class="admin-card" style="text-align:center;box-shadow:var(--shadow)">
      <div style="width:64px;height:64px;border-radius:50%;background:#ECFDF3;color:var(--green);display:grid;place-items:center;font-size:32px;margin:0 auto 14px"><i class="bi bi-check-lg"></i></div>
      <h1 class="section-title" style="font-size:1.6rem">Order received!</h1>
      <p class="section-sub" style="margin:8px auto 0">Thank you. We've saved your order and will confirm on WhatsApp shortly.</p>
      <div style="display:inline-block;margin-top:16px;background:var(--violet-lt);color:var(--violet-dk);font-family:'Sora';font-weight:800;font-size:1.1rem;padding:10px 20px;border-radius:12px">
        <?= e($order['order_no']) ?>
      </div>
    </div>

    <div class="admin-card" style="margin-top:18px">
      <h3 style="font-size:1.05rem;margin-bottom:12px">Order summary</h3>
      <?php foreach ($items as $it): ?>
      <div class="co-item">
        <div class="co-item-main">
          <div class="co-item-name"><?= e($it['name']) ?><?= $it['variant'] ? ' · '.e($it['variant']) : '' ?></div>
          <div class="co-item-sub"><?= (int)$it['qty'] ?> <?= e($it['unit_label']) ?> × <?= ugx($it['unit_price']) ?><?= $it['design'] ? ' + design '.ugx($it['design_fee']) : '' ?></div>
        </div>
        <div class="co-item-total"><?= ugx($it['line_total']) ?></div>
      </div>
      <?php endforeach; ?>
      <div class="co-line"><span>Items</span><strong><?= ugx($order['subtotal']) ?></strong></div>
      <?php if ($order['design_total'] > 0): ?><div class="co-line"><span>Design</span><strong><?= ugx($order['design_total']) ?></strong></div><?php endif; ?>
      <div class="co-line"><span>Delivery</span><strong><?= $order['delivery_method']==='delivery_far' ? 'Confirmed on WhatsApp' : 'Free' ?></strong></div>
      <div class="co-line co-total"><span>Total</span><strong><?= ugx($order['total']) ?></strong></div>
      <div class="co-line co-deposit"><span>50% deposit to begin</span><strong><?= ugx($order['deposit']) ?></strong></div>

      <ul class="detail-perks" style="margin-top:18px">
        <li><i class="bi bi-truck" style="color:var(--violet)"></i> <?= e($deliveryLabel) ?></li>
        <li><i class="bi bi-cash-coin" style="color:var(--violet)"></i> Payment: <?= e($order['payment_method']) ?> · pay 50% deposit to start</li>
        <li><i class="bi bi-clock" style="color:var(--violet)"></i> ~5 hour turnaround when you provide the soft copy</li>
      </ul>

      <div class="detail-cta" style="margin-top:18px">
        <a href="<?= $waUrl ?: wa_link('Hello Cheapa Studio! I just placed order '.$order['order_no'].'.') ?>" target="_blank" rel="noopener" class="btn btn-wa btn-lg"><i class="bi bi-whatsapp"></i> Confirm on WhatsApp</a>
        <a href="<?= SITE_URL ?>/track-order.php?no=<?= rawurlencode($order['order_no']) ?>" class="btn btn-ghost btn-lg"><i class="bi bi-search"></i> Track this order</a>
      </div>
    </div>

    <p style="text-align:center;margin-top:18px"><a href="<?= SITE_URL ?>/shop.php" style="color:var(--violet-dk);font-weight:600">← Continue shopping</a></p>
  </div>
</section>

<script>
  // clear the cart now the order is placed
  try { if (window.CHEAPA_CART) window.CHEAPA_CART.clear(); else localStorage.removeItem('cheapa_cart_v2'); } catch (e) {}
  <?php if ($waUrl): ?>setTimeout(function(){ window.open(<?= json_encode($waUrl) ?>, '_blank'); }, 600);<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
