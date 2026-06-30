<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Checkout';
include 'includes/header.php';
?>

<section class="section">
  <div class="container" style="max-width:920px">
    <nav class="breadcrumb">
      <a href="<?= SITE_URL ?>/shop.php">Shop</a><i class="bi bi-chevron-right"></i>
      <span style="color:var(--text)">Checkout</span>
    </nav>

    <h1 class="section-title" style="font-size:1.8rem;margin-bottom:6px">Checkout</h1>
    <p class="section-sub" style="margin:0 0 24px">Order in-app or on WhatsApp — your choice. Either way we save your order and confirm the 50% deposit to begin.</p>

    <!-- Empty state -->
    <div class="no-results" id="checkoutEmpty" hidden>
      <i class="bi bi-bag-x"></i>
      <p>Your cart is empty.</p>
      <a href="<?= SITE_URL ?>/shop.php" class="btn btn-primary" style="margin-top:12px">Browse the Shop</a>
    </div>

    <form method="post" action="<?= SITE_URL ?>/place-order.php" id="checkoutForm" class="checkout-grid" hidden>
      <input type="hidden" name="cart" id="cartPayload">
      <input type="hidden" name="channel" id="channelField" value="in-app">

      <!-- Left: details -->
      <div class="admin-card" style="box-shadow:var(--shadow-sm)">
        <h3 style="font-size:1.1rem;margin-bottom:14px">Your details</h3>
        <div class="alert alert-error" id="coError" hidden style="margin-bottom:14px"></div>
        <div class="form-row">
          <div class="form-group"><label>Your name <span class="req">*</span></label><input name="customer_name" id="coName" class="form-control" placeholder="Your name"></div>
          <div class="form-group"><label>Business name <span class="req">*</span></label><input name="business_name" id="coBiz" class="form-control" placeholder="Your business"></div>
        </div>
        <div class="form-hint" style="margin:-8px 0 14px"><i class="bi bi-info-circle"></i> Enter your name or your business name (at least one is required).</div>
        <div class="form-row">
          <div class="form-group"><label>WhatsApp number <span class="req">*</span></label><input name="whatsapp" id="coWa" class="form-control" required placeholder="e.g. 0753 168599"></div>
          <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" placeholder="Optional"></div>
        </div>

        <h3 style="font-size:1.1rem;margin:18px 0 12px">Delivery</h3>
        <div class="svc-pills" style="grid-template-columns:1fr">
          <div class="svc-pill"><input type="radio" name="delivery_method" id="dm1" value="pickup" checked><label for="dm1"><i class="bi bi-shop"></i> Free pickup at our location</label></div>
          <div class="svc-pill"><input type="radio" name="delivery_method" id="dm2" value="delivery_kampala"><label for="dm2"><i class="bi bi-truck"></i> Delivery within Kampala — free</label></div>
          <div class="svc-pill"><input type="radio" name="delivery_method" id="dm3" value="delivery_far"><label for="dm3"><i class="bi bi-geo-alt"></i> Outside Kampala — fee confirmed on WhatsApp</label></div>
        </div>
        <div class="form-group" id="addrGroup" style="margin-top:12px;display:none">
          <label>Delivery address / area</label>
          <input name="delivery_address" class="form-control" placeholder="Area, landmark, town">
        </div>

        <h3 style="font-size:1.1rem;margin:18px 0 12px">Payment</h3>
        <div class="svc-pills">
          <div class="svc-pill"><input type="radio" name="payment_method" id="pm1" value="Mobile Money" checked><label for="pm1"><i class="bi bi-phone"></i> Mobile Money</label></div>
          <div class="svc-pill"><input type="radio" name="payment_method" id="pm2" value="Cash"><label for="pm2"><i class="bi bi-cash"></i> Cash</label></div>
        </div>

        <div class="form-group" style="margin-top:14px"><label>Notes (optional)</label><textarea name="notes" class="form-control" placeholder="Anything we should know?"></textarea></div>
      </div>

      <!-- Right: summary -->
      <div class="admin-card checkout-summary" style="box-shadow:var(--shadow-sm)">
        <h3 style="font-size:1.1rem;margin-bottom:12px">Order summary</h3>
        <div id="checkoutItems"></div>
        <div class="co-line"><span>Items</span><strong id="coSubtotal"><?= CURRENCY ?> 0</strong></div>
        <div class="co-line" id="coDesignLine" hidden><span>Design</span><strong id="coDesign"><?= CURRENCY ?> 0</strong></div>
        <div class="co-line"><span>Delivery</span><strong id="coDelivery">Free</strong></div>
        <div class="co-line co-total"><span>Total</span><strong id="coTotal"><?= CURRENCY ?> 0</strong></div>
        <div class="co-line co-deposit"><span>50% deposit to begin</span><strong id="coDeposit"><?= CURRENCY ?> 0</strong></div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" id="placeInApp" style="margin-top:14px"><i class="bi bi-bag-check"></i> Place Order</button>
        <button type="submit" class="btn btn-wa btn-block" id="placeWa" style="margin-top:10px"><i class="bi bi-whatsapp"></i> Order on WhatsApp</button>
        <p class="cart-note" style="margin-top:12px"><i class="bi bi-shield-check"></i> Your order is saved and you get an order number to track it.</p>
      </div>
    </form>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
