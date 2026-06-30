<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$slug = trim($_GET['slug'] ?? '');
$id   = (int)($_GET['id'] ?? 0);

if ($slug !== '') {
    $stmt = $pdo->prepare("SELECT * FROM packs WHERE slug = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$slug]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM packs WHERE id = ? AND is_active = 1 LIMIT 1");
    $stmt->execute([$id]);
}
$pack = $stmt->fetch();

if (!$pack) {
    http_response_code(404);
    $pageTitle = 'Pack not found';
    include 'includes/header.php';
    echo '<section class="section"><div class="container no-results"><i class="bi bi-box-seam"></i><h1 class="section-title">Pack not found</h1><a href="'.SITE_URL.'/packs.php" class="btn btn-primary" style="margin-top:18px"><i class="bi bi-arrow-left"></i> Back to Packs</a></div></section>';
    include 'includes/footer.php';
    exit;
}

$features = website_first(array_values(array_filter(array_map('trim', explode("\n", $pack['features'])))));

// Editable "what's inside" items (managed in the admin). Fall back to the
// auto-mapped library images if none have been set up for this pack yet.
$packItems = $pdo->prepare("SELECT * FROM pack_items WHERE pack_id = ? ORDER BY sort_order, id");
$packItems->execute([$pack['id']]);
$packItems = website_first($packItems->fetchAll(), fn($r) => $r['label']);

$others = $pdo->prepare("SELECT * FROM packs WHERE id <> ? AND is_active = 1 ORDER BY sort_order LIMIT 3");
$others->execute([$pack['id']]);
$others = $others->fetchAll();

$pageTitle = $pack['name'];
$pageDesc  = $pack['tagline'] . ' — ' . $pack['name'] . ' from ' . ugx($pack['price_ugx']);
$ogImage   = img_url('packs', $pack['image']) ?: null;
$shareUrl  = SITE_URL . '/pack.php?slug=' . rawurlencode($pack['slug']);
include 'includes/header.php';
?>

<section class="section">
  <div class="container">
    <nav class="breadcrumb">
      <a href="<?= SITE_URL ?>/">Home</a><i class="bi bi-chevron-right"></i>
      <a href="<?= SITE_URL ?>/packs.php">Packs</a><i class="bi bi-chevron-right"></i>
      <span style="color:var(--text)"><?= e($pack['name']) ?></span>
    </nav>

    <div class="detail-wrap">
      <div class="detail-gallery">
        <span class="gallery-badge"><?= e($pack['stage']) ?> stage</span>
        <?php if ($img = img_url('packs', $pack['image'])): ?>
          <img src="<?= e($img) ?>" alt="<?= e($pack['name']) ?>">
        <?php else: ?>
          <i class="product-icon bi bi-box-seam"></i>
        <?php endif; ?>
      </div>

      <div class="detail-info">
        <?php if ($pack['is_featured']): ?><span class="detail-stage" style="color:var(--amber)"><i class="bi bi-star-fill"></i> Most recommended</span><?php endif; ?>
        <h1><?= e($pack['name']) ?></h1>
        <p class="detail-desc" style="margin:8px 0 0"><?= e($pack['tagline']) ?></p>
        <div class="detail-price"><?= ugx($pack['price_ugx']) ?> <small>complete package</small></div>
        <?php if ($pack['best_for']): ?><div class="detail-bestfor"><i class="bi bi-people-fill"></i> Best for: <?= e($pack['best_for']) ?></div><?php endif; ?>

        <div class="detail-cta">
          <button class="btn btn-primary btn-lg" data-add-cart data-type="pack"
                  data-id="<?= (int)$pack['id'] ?>" data-name="<?= e($pack['name']) ?>"
                  data-price="<?= (int)$pack['price_ugx'] ?>" data-icon="bi-box-seam">
            <i class="bi bi-bag-plus"></i> Add to Cart — <?= ugx($pack['price_ugx']) ?>
          </button>
        </div>
        <div class="form-hint" style="margin-top:10px"><i class="bi bi-info-circle"></i> At checkout you add your business details and choose to order in-app or on WhatsApp.</div>

        <ul class="detail-perks">
          <li><i class="bi bi-check-circle-fill"></i> <?= count($features) ?> items in one bundle — and you save vs. buying separately</li>
          <li><i class="bi bi-check-circle-fill"></i> One consistent brand across everything</li>
          <li><i class="bi bi-check-circle-fill"></i> Pay 50% deposit to begin</li>
        </ul>

        <div class="share-row">
          <span>Share:</span>
          <button type="button" class="share-pill share-btn" data-share-url="<?= e($shareUrl) ?>" data-share-title="<?= e($pack['name']) ?>"><i class="bi bi-share"></i> Share this pack</button>
        </div>
      </div>
    </div>

    <!-- What's inside -->
    <div style="margin-top:50px">
      <div class="text-center" style="margin-bottom:24px">
        <span class="eyebrow"><i class="bi bi-box2-heart"></i> What's Inside</span>
        <h2 class="section-title" style="font-size:1.7rem">Everything in the <?= e($pack['name']) ?></h2>
        <p class="section-sub">Here's exactly what you get — designed, delivered and ready to use.</p>
      </div>
      <div class="inside-grid">
        <?php if ($packItems): ?>
          <?php foreach ($packItems as $i => $it):
            $auto = pack_item_meta($it['label']); ?>
          <div class="inside-card">
            <div class="inside-media">
              <span class="inside-num"><?= $i + 1 ?></span>
              <?php if ($mi = img_url('pack-items', $it['image'])): ?>
                <img src="<?= e($mi) ?>" alt="<?= e($it['label']) ?>" loading="lazy">
              <?php else: ?>
                <span style="display:grid;place-items:center;height:100%;color:var(--violet);font-size:32px"><i class="bi <?= e($auto['icon']) ?>"></i></span>
              <?php endif; ?>
            </div>
            <div class="inside-body">
              <b><?= e($it['label']) ?></b>
              <?php if (trim($it['blurb'])): ?><p><?= e($it['blurb']) ?></p><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <?php foreach ($features as $i => $f): $m = pack_item_meta($f); ?>
          <div class="inside-card">
            <div class="inside-media">
              <span class="inside-num"><?= $i + 1 ?></span>
              <?php if ($mi = img_url('items', $m['image'])): ?>
                <img src="<?= e($mi) ?>" alt="<?= e($m['label']) ?>" loading="lazy">
              <?php else: ?>
                <span style="display:grid;place-items:center;height:100%;color:var(--violet);font-size:32px"><i class="bi <?= e($m['icon']) ?>"></i></span>
              <?php endif; ?>
            </div>
            <div class="inside-body"><b><?= e($m['label']) ?></b><p><?= e($m['blurb']) ?></p></div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Other packs -->
    <?php if ($others): ?>
    <div style="margin-top:50px">
      <h2 class="section-title" style="font-size:1.4rem;margin-bottom:18px">Compare other packs</h2>
      <div class="pack-grid">
        <?php foreach ($others as $p):
          $pf = array_filter(array_map('trim', explode("\n", $p['features']))); ?>
        <div class="pack-card <?= $p['is_featured'] ? 'featured' : '' ?>">
          <?php if ($p['is_featured']): ?><span class="pack-flag">★ Recommended</span><?php endif; ?>
          <a href="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" style="display:block">
            <div class="pack-visual">
              <?php if ($pimg = img_url('packs', $p['image'])): ?><img src="<?= e($pimg) ?>" alt="<?= e($p['name']) ?>" loading="lazy"><?php else: ?><i class="bi bi-box-seam"></i><?php endif; ?>
              <span class="pack-stage"><?= e($p['stage']) ?></span>
              <button type="button" class="card-share share-btn" aria-label="Share" data-share-url="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" data-share-title="<?= e($p['name']) ?>"><i class="bi bi-share-fill"></i></button>
            </div>
          </a>
          <div class="pack-body">
            <div class="pack-name"><?= e($p['name']) ?></div>
            <div class="pack-price"><?= ugx($p['price_ugx']) ?></div>
            <div class="pack-actions">
              <a href="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" class="btn btn-ghost">View what's inside <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
