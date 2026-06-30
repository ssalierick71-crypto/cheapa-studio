<?php if (!defined('SITE_NAME')) require_once dirname(__DIR__) . '/config.php'; ?>
<!-- ════════════════════════ CART DRAWER ════════════════════════ -->
<div class="cart-drawer" id="cartDrawer" aria-hidden="true">
  <div class="cart-backdrop" data-close-cart></div>
  <aside class="cart-panel" role="dialog" aria-modal="true" aria-label="Your cart">
    <div class="cart-head">
      <h3><i class="bi bi-bag"></i> Your Cart</h3>
      <button class="icon-btn" data-close-cart aria-label="Close cart"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="cart-items" id="cartItems"><!-- JS-rendered --></div>

    <div class="cart-empty" id="cartEmpty">
      <i class="bi bi-bag-x"></i>
      <p>Your cart is empty.</p>
      <a href="<?= SITE_URL ?>/shop.php" class="btn btn-primary btn-sm" data-close-cart>Browse the Shop</a>
    </div>

    <div class="cart-foot" id="cartFoot" hidden>
      <div class="cart-total">
        <span>Total</span>
        <strong id="cartTotal"><?= CURRENCY ?> 0</strong>
      </div>
      <p class="cart-note">At checkout you choose to order in-app or on WhatsApp. We confirm your order &amp; the 50% deposit to begin.</p>
      <a href="#" class="btn btn-primary btn-block" id="cartCheckoutBtn">
        <i class="bi bi-bag-check"></i> Proceed to Checkout
      </a>
      <button class="cart-clear" id="cartClearBtn">Clear cart</button>
    </div>
  </aside>
</div>
