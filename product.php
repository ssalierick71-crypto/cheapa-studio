<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$slug = trim($_GET['slug'] ?? '');
$id   = (int)($_GET['id'] ?? 0);

if ($slug !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE slug = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$slug]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$id]);
}
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product not found';
    include 'includes/header.php';
    echo '<section class="section"><div class="container no-results"><i class="bi bi-bag-x"></i><h1 class="section-title">Product not found</h1><a href="'.SITE_URL.'/shop.php" class="btn btn-primary" style="margin-top:18px"><i class="bi bi-arrow-left"></i> Back to Shop</a></div></section>';
    include 'includes/footer.php';
    exit;
}

$catIcons = ['Branding'=>'bi-vector-pen','Print'=>'bi-printer','Digital'=>'bi-phone','Web'=>'bi-window'];
$icon = $catIcons[$product['category']] ?? 'bi-bag';
$variants = product_variants($product['variants'] ?? '');
$configurable = is_configurable($product);
$basePrice = (int)$product['price_ugx'];

$related = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id <> ? AND is_active = 1 ORDER BY sort_order LIMIT 4");
$related->execute([$product['category'], $product['id']]);
$related = $related->fetchAll();

$pageTitle = $product['name'];
$pageDesc  = $product['description'];
$ogImage   = img_url('products', $product['image']) ?: null;
$shareUrl  = SITE_URL . '/product.php?slug=' . rawurlencode($product['slug']);
include 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <nav class="breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a><i class="bi bi-chevron-right"></i>
      <a href="<?= SITE_URL ?>/shop.php">Shop</a><i class="bi bi-chevron-right"></i>
      <span style="color:var(--text)"><?= e($product['name']) ?></span>
    </nav>

    <div class="detail-wrap">
      <div class="detail-gallery">
        <span class="gallery-badge"><?= e($product['category']) ?></span>
        <?php if ($img = img_url('products', $product['image'])): ?>
          <img src="<?= e($img) ?>" alt="<?= e($product['name']) ?>">
        <?php else: ?>
          <i class="product-icon bi <?= $icon ?>"></i>
        <?php endif; ?>
      </div>

      <div class="detail-info">
        <span class="detail-cat"><i class="bi <?= $icon ?>"></i> <?= e($product['category']) ?></span>
        <h1><?= e($product['name']) ?></h1>

        <?php if ($configurable): ?>
          <div class="detail-price">
            <?= CURRENCY ?> <?= number_format($basePrice) ?><small> / <?= e(rtrim($product['unit_label'], 's')) ?></small>
          </div>
          <div class="form-hint" style="margin-bottom:18px"><i class="bi bi-info-circle"></i> Minimum order: <?= (int)$product['moq'] ?> <?= e($product['unit_label']) ?></div>
        <?php else: ?>
          <div class="detail-price"><?= ugx($basePrice) ?> <small>starting price</small></div>
        <?php endif; ?>

        <p class="detail-desc"><?= e($product['description']) ?></p>

        <?php if ($configurable): ?>
        <!-- ── Configurator ── -->
        <div class="configurator" id="productConfig"
             data-id="<?= (int)$product['id'] ?>"
             data-name="<?= e($product['name']) ?>"
             data-icon="<?= $icon ?>"
             data-unit-type="<?= e($product['unit_type']) ?>"
             data-unit-label="<?= e($product['unit_label']) ?>"
             data-base-price="<?= $basePrice ?>"
             data-moq="<?= (int)$product['moq'] ?>"
             data-step="<?= (int)$product['step'] ?>"
             data-design-fee="<?= (int)$product['design_fee'] ?>">

          <?php if ($variants): ?>
          <div class="cfg-group">
            <label class="cfg-label">Choose option</label>
            <div class="cfg-variants">
              <?php foreach ($variants as $i => $v): ?>
              <label class="cfg-variant">
                <input type="radio" name="cfgVariant" value="<?= (int)$v['price'] ?>" data-label="<?= e($v['label']) ?>" <?= $i===0?'checked':'' ?>>
                <span><b><?= e($v['label']) ?></b><small><?= ugx($v['price']) ?> / <?= e(rtrim($product['unit_label'],'s')) ?></small></span>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div class="cfg-group">
            <label class="cfg-label">Quantity (<?= e($product['unit_label']) ?>)</label>
            <div class="cfg-qty">
              <button type="button" id="cfgMinus" aria-label="Decrease">−</button>
              <input type="number" id="cfgQty" value="<?= (int)$product['moq'] ?>" min="<?= (int)$product['moq'] ?>" step="<?= (int)$product['step'] ?>">
              <button type="button" id="cfgPlus" aria-label="Increase">+</button>
            </div>
          </div>

          <?php if ($product['design_available']): ?>
          <div class="cfg-group">
            <label class="cfg-toggle">
              <input type="checkbox" id="cfgDesign">
              <span>
                <b>Design it for me <span class="cfg-fee">+<?= ugx($product['design_fee']) ?></span></b>
                <small>Leave unchecked if you already have your soft copy (ready in ~5 hours).</small>
              </span>
            </label>
          </div>
          <?php endif; ?>

          <div class="cfg-total">
            <span>Total</span>
            <strong id="cfgTotal"><?= CURRENCY ?> 0</strong>
          </div>

          <div class="detail-cta">
            <button class="btn btn-primary btn-lg" id="cfgAdd"><i class="bi bi-bag-plus"></i> Add to Cart</button>
          </div>
        </div>

        <?php else: ?>
        <!-- ── Fixed-price product ── -->
        <div class="detail-cta">
          <button class="btn btn-primary btn-lg"
                  data-add-cart
                  data-id="<?= (int)$product['id'] ?>"
                  data-name="<?= e($product['name']) ?>"
                  data-price="<?= $basePrice ?>"
                  data-icon="<?= $icon ?>">
            <i class="bi bi-bag-plus"></i> Add to Cart
          </button>
          <a href="<?= wa_link("Hello Cheapa Studio! I'd like to order: {$product['name']} (".ugx($basePrice)."). Please tell me the next step.") ?>" target="_blank" rel="noopener" class="btn btn-wa btn-lg"><i class="bi bi-whatsapp"></i> Order on WhatsApp</a>
        </div>
        <?php endif; ?>

        <ul class="detail-perks">
          <li><i class="bi bi-check-circle-fill"></i> <?= $configurable ? 'Print-ready and delivered fast' : 'Delivered in editable + print-ready formats' ?></li>
          <li><i class="bi bi-clock-fill" style="color:var(--violet)"></i> ~5 hour turnaround when you provide the soft copy</li>
          <li><i class="bi bi-truck" style="color:var(--violet)"></i> Free pickup &amp; free delivery within Kampala</li>
          <li><i class="bi bi-shield-check" style="color:var(--green)"></i> Pay 50% deposit to begin — revisions until you're happy</li>
        </ul>

        <div class="share-row">
          <span>Share:</span>
          <button type="button" class="share-pill share-btn" data-share-url="<?= e($shareUrl) ?>" data-share-title="<?= e($product['name']) ?>"><i class="bi bi-share"></i> Share this product</button>
        </div>
      </div>
    </div>

    <?php if ($related): ?>
    <div style="margin-top:48px">
      <h2 class="section-title" style="font-size:1.4rem;margin-bottom:18px">You may also like</h2>
      <div class="product-grid">
        <?php foreach ($related as $pr):
          $ricon = $catIcons[$pr['category']] ?? 'bi-bag';
          $rconf = is_configurable($pr); ?>
        <article class="product-card">
          <a href="<?= SITE_URL ?>/product.php?slug=<?= e($pr['slug']) ?>" class="product-link">
            <div class="product-media">
              <?php if ($rimg = img_url('products', $pr['image'])): ?><img src="<?= e($rimg) ?>" alt="<?= e($pr['name']) ?>" loading="lazy"><?php else: ?><i class="product-icon bi <?= $ricon ?>"></i><?php endif; ?>
              <span class="product-badge"><?= e($pr['category']) ?></span>
              <button type="button" class="card-share share-btn" aria-label="Share" data-share-url="<?= SITE_URL ?>/product.php?slug=<?= e($pr['slug']) ?>" data-share-title="<?= e($pr['name']) ?>"><i class="bi bi-share-fill"></i></button>
            </div>
            <div class="product-info"><h3 class="product-name"><?= e($pr['name']) ?></h3></div>
          </a>
          <div class="product-foot">
            <span class="price-pill"><?= $rconf ? 'from '.number_format($pr['price_ugx']).'/=' : ugx($pr['price_ugx']) ?></span>
            <a href="<?= SITE_URL ?>/product.php?slug=<?= e($pr['slug']) ?>" class="buy-btn">View <span class="arrow"><i class="bi bi-arrow-up-right"></i></span></a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
