<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Design Shop';
$pageDesc  = 'Order individual branding designs — logos, business cards, flyers, posters, social posts and more. Add to cart and checkout on WhatsApp.';

$products = $pdo->query("SELECT * FROM products WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();

$catIcons = [
  'Branding' => 'bi-vector-pen',
  'Print'    => 'bi-printer',
  'Digital'  => 'bi-phone',
  'Web'      => 'bi-window',
];
// Short accent sub-line per category (the catchy "Own the Airforce" line).
$catSub = [
  'Branding' => 'Look the part',
  'Print'    => 'Ready to print',
  'Digital'  => 'Built for the feed',
  'Web'      => 'Get online fast',
];
// First two products are flagged as best sellers for the badge.
$bestSellers = 2;

include 'includes/header.php';
?>

<section class="hero" style="padding:42px 0 34px">
  <div class="container hero-inner">
    <span class="eyebrow"><i class="bi bi-bag-heart"></i> Design Shop</span>
    <h1 style="font-size:clamp(1.7rem,6vw,2.6rem)">Order designs, <span class="grad-text">fast</span></h1>
    <p class="hero-sub">Pick what you need, add to cart, and check out on WhatsApp. Simple.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="shop-toolbar">
      <div class="shop-search">
        <i class="bi bi-search"></i>
        <input type="search" id="shopSearch" placeholder="Search logos, cards, flyers…" autocomplete="off">
      </div>
      <div class="shop-filters">
        <button class="filter-chip active" data-cat="all">All</button>
        <button class="filter-chip" data-cat="Branding">Branding</button>
        <button class="filter-chip" data-cat="Print">Print</button>
        <button class="filter-chip" data-cat="Digital">Digital</button>
        <button class="filter-chip" data-cat="Web">Web</button>
      </div>
    </div>

    <div class="product-grid">
      <?php foreach ($products as $idx => $pr):
        $icon  = $catIcons[$pr['category']] ?? 'bi-bag';
        $badge = $idx < $bestSellers ? 'Best Seller' : $pr['category']; ?>
      <?php $conf = is_configurable($pr); ?>
      <article class="product-card" data-cat="<?= e($pr['category']) ?>" data-name="<?= e($pr['name']) ?>" data-search="<?= e(strtolower($pr['name'].' '.$pr['category'].' '.$pr['description'])) ?>">
        <a href="<?= SITE_URL ?>/product.php?slug=<?= e($pr['slug']) ?>" class="product-link">
          <div class="product-media">
            <?php if ($img = img_url('products', $pr['image'])): ?>
              <img src="<?= e($img) ?>" alt="<?= e($pr['name']) ?>" loading="lazy">
            <?php else: ?>
              <i class="product-icon bi <?= $icon ?>"></i>
            <?php endif; ?>
            <span class="product-badge"><?= e($badge) ?></span>
            <span class="product-brand"><i class="bi <?= $icon ?>"></i></span>
            <div class="product-dots"><span class="active"></span><span></span><span></span><span></span></div>
            <button type="button" class="card-share share-btn" aria-label="Share <?= e($pr['name']) ?>"
                    data-share-url="<?= SITE_URL ?>/product.php?slug=<?= e($pr['slug']) ?>"
                    data-share-title="<?= e($pr['name']) ?>"><i class="bi bi-share-fill"></i></button>
          </div>
          <div class="product-info">
            <h3 class="product-name"><?= e($pr['name']) ?></h3>
            <div class="product-sub"><?= e($catSub[$pr['category']] ?? $pr['category']) ?></div>
            <p class="product-desc"><?= e($pr['description']) ?></p>
          </div>
        </a>
        <div class="product-foot">
          <span class="price-pill"><?php if ($conf): ?>from <?= number_format($pr['price_ugx']) ?>/=<?php else: ?><?= ugx($pr['price_ugx']) ?><?php endif; ?></span>
          <?php if ($conf): ?>
            <a href="<?= SITE_URL ?>/product.php?slug=<?= e($pr['slug']) ?>" class="buy-btn">Order <span class="arrow"><i class="bi bi-arrow-up-right"></i></span></a>
          <?php else: ?>
            <button class="buy-btn" data-add-cart data-id="<?= (int)$pr['id'] ?>" data-name="<?= e($pr['name']) ?>" data-price="<?= (int)$pr['price_ugx'] ?>" data-icon="<?= $icon ?>">
              Add <span class="arrow"><i class="bi bi-arrow-up-right"></i></span>
            </button>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="no-results" id="shopNoResults" hidden>
      <i class="bi bi-search"></i>
      <p>No products match your search. Try another word or clear the filter.</p>
    </div>

    <div class="text-center" style="margin-top:34px">
      <p class="section-sub">Need a bundle instead? Packs save you more.</p>
      <a href="<?= SITE_URL ?>/packs.php" class="btn btn-ghost" style="margin-top:12px">See Business Growth Packs <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
