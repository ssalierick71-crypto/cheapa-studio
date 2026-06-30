<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';
if (session_status() === PHP_SESSION_NONE) session_start();

function bail($msg = '') { header('Location: ' . SITE_URL . '/shop.php' . ($msg ? '?err=' . rawurlencode($msg) : '')); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') bail();

$whatsapp = trim($_POST['whatsapp'] ?? '');
if ($whatsapp === '') bail('Please enter your WhatsApp number.');

$customerName = substr(trim($_POST['customer_name'] ?? ''), 0, 120);
$businessName = substr(trim($_POST['business_name'] ?? ''), 0, 150);
if ($customerName === '' && $businessName === '') bail('Please enter your name or your business name.');

$payload = json_decode($_POST['cart'] ?? '[]', true);
if (!is_array($payload) || !$payload) bail('Your cart is empty.');

// Recompute every line from the database (never trust client prices).
$items = [];
$subtotal = 0; $designTotal = 0;
$prodStmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1");
$packStmt = $pdo->prepare("SELECT * FROM packs WHERE id = ? AND is_active = 1 LIMIT 1");

foreach ($payload as $row) {
    $pid = (int)($row['id'] ?? 0);
    if (!$pid) continue;
    $type = (($row['type'] ?? 'product') === 'pack') ? 'pack' : 'product';

    if ($type === 'pack') {
        // Packs are fixed-price bundles (no variants, no per-unit design fee).
        $packStmt->execute([$pid]);
        $p = $packStmt->fetch();
        if (!$p) continue;
        $unitPrice = (int)$p['price_ugx'];
        $qty = max(1, (int)($row['qty'] ?? 1));
        $lineTotal = $unitPrice * $qty;
        $subtotal += $lineTotal;
        $items[] = [
            'product_id' => null, 'name' => $p['name'], 'variant' => 'Business Growth Pack',
            'unit_type' => 'fixed', 'unit_label' => '', 'unit_price' => $unitPrice,
            'qty' => $qty, 'design' => 0, 'design_fee' => 0, 'line_total' => $lineTotal,
        ];
        continue;
    }

    $prodStmt->execute([$pid]);
    $p = $prodStmt->fetch();
    if (!$p) continue;

    $variantLabel = trim((string)($row['variant'] ?? ''));
    $unitPrice = product_unit_price($p, $variantLabel ?: null);

    $moq  = max(1, (int)$p['moq']);
    $step = max(1, (int)$p['step']);
    $qty  = (int)($row['qty'] ?? $moq);
    if ($qty < $moq) $qty = $moq;
    $qty = (int)(round($qty / $step) * $step);
    if ($qty < $moq) $qty = $moq;

    $design = (!empty($row['design']) && $p['design_available']) ? 1 : 0;
    $designFee = $design ? (int)$p['design_fee'] : 0;

    $lineCore = $unitPrice * $qty;
    $lineTotal = $lineCore + $designFee;
    $subtotal   += $lineCore;
    $designTotal += $designFee;

    $items[] = [
        'product_id' => $pid,
        'name'       => $p['name'],
        'variant'    => $variantLabel,
        'unit_type'  => $p['unit_type'],
        'unit_label' => $p['unit_label'],
        'unit_price' => $unitPrice,
        'qty'        => $qty,
        'design'     => $design,
        'design_fee' => $designFee,
        'line_total' => $lineTotal,
    ];
}
if (!$items) bail('Your cart is empty.');

$deliveryMethod = in_array($_POST['delivery_method'] ?? '', ['pickup','delivery_kampala','delivery_far'], true) ? $_POST['delivery_method'] : 'pickup';
$deliveryFee = 0; // pickup & within-Kampala are free; far is confirmed on WhatsApp
$total   = $subtotal + $designTotal + $deliveryFee;
$deposit = (int)ceil($total / 2);
$channel = ($_POST['channel'] ?? 'in-app') === 'whatsapp' ? 'whatsapp' : 'in-app';
$payment = substr(trim($_POST['payment_method'] ?? 'Mobile Money'), 0, 40);

// Insert order
$pdo->prepare(
  "INSERT INTO orders (order_no, customer_name, business_name, whatsapp, email, delivery_method, delivery_address, subtotal, design_total, delivery_fee, total, deposit, payment_method, channel, status, notes)
   VALUES ('', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New', ?)"
)->execute([
    $customerName,
    $businessName,
    $whatsapp,
    substr(trim($_POST['email'] ?? ''), 0, 150),
    $deliveryMethod,
    substr(trim($_POST['delivery_address'] ?? ''), 0, 300),
    $subtotal, $designTotal, $deliveryFee, $total, $deposit,
    $payment, $channel,
    substr(trim($_POST['notes'] ?? ''), 0, 1000),
]);
$orderId = last_id($pdo, 'orders');
$orderNo = order_number($orderId);
$pdo->prepare("UPDATE orders SET order_no = ? WHERE id = ?")->execute([$orderNo, $orderId]);

$ins = $pdo->prepare(
  "INSERT INTO order_items (order_id, product_id, name, variant, unit_type, unit_label, unit_price, qty, design, design_fee, line_total)
   VALUES (?,?,?,?,?,?,?,?,?,?,?)"
);
foreach ($items as $it) {
    $ins->execute([$orderId, $it['product_id'], $it['name'], $it['variant'], $it['unit_type'], $it['unit_label'], $it['unit_price'], $it['qty'], $it['design'], $it['design_fee'], $it['line_total']]);
}

// Build the WhatsApp hand-off message (used when buyer chose WhatsApp)
$deliveryLabel = ['pickup'=>'Pickup','delivery_kampala'=>'Delivery (Kampala) - free','delivery_far'=>'Delivery (outside Kampala) - fee TBC'][$deliveryMethod];
$lines = "Hello Cheapa Studio! I placed order $orderNo:%0A%0A";
foreach ($items as $it) {
    $lines .= "• {$it['name']}" . ($it['variant'] ? " ({$it['variant']})" : '') . " — {$it['qty']} {$it['unit_label']}" . ($it['design'] ? " + design" : '') . " = " . ugx($it['line_total']) . "%0A";
}
$lines .= "%0ATotal: " . ugx($total) . "%0ADeposit (50%): " . ugx($deposit) . "%0ADelivery: $deliveryLabel%0APayment: $payment%0A%0APlease confirm.";
$waText = str_replace(' ', '%20', $lines);

$_SESSION['order_done'] = $orderId;
$_SESSION['order_wa'] = $channel === 'whatsapp'
    ? 'https://wa.me/' . preg_replace('/\D/', '', cfg('whatsapp_number')) . '?text=' . $waText
    : '';

header('Location: ' . SITE_URL . '/order-confirmation.php');
exit;
