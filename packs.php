<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Business Growth Packs';
$pageDesc  = 'Pre-built branding bundles for Ugandan entrepreneurs — Launch, Visibility, Growth and Authority packs from UGX 100,000.';

$packs = $pdo->query("SELECT * FROM packs WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();

$addons = [
  ['Extra Flyers (100pcs)', 60000, 'bi-file-earmark-richtext'],
  ['Business Cards (200pcs)', 50000, 'bi-person-vcard'],
  ['Receipt Book', 45000, 'bi-receipt'],
  ['Banner Design', 55000, 'bi-flag'],
  ['Company Profile', 120000, 'bi-building'],
  ['Extra Website Page', 80000, 'bi-window-stack'],
  ['Social Media Kit (5 posts)', 35000, 'bi-instagram'],
];

include 'includes/header.php';
?>

<section class="hero" style="padding:46px 0 40px">
  <div class="container hero-inner">
    <span class="eyebrow"><i class="bi bi-box-seam"></i> Business Growth Solutions</span>
    <h1 style="font-size:clamp(1.7rem,6vw,2.7rem)">Branding bundles for<br><span class="grad-text">Ugandan entrepreneurs</span></h1>
    <p class="hero-sub">Pick the pack that matches where your business is right now. Every pack saves you money versus buying items one by one.</p>
  </div>
</section>

<section class="section">
  <div class="container">

    <!-- Stage selector -->
    <div class="stage-selector">
      <button class="stage-chip active" data-stage="all">All stages</button>
      <button class="stage-chip" data-stage="Starting">Starting</button>
      <button class="stage-chip" data-stage="Growing">Growing</button>
      <button class="stage-chip" data-stage="Established">Established</button>
      <button class="stage-chip" data-stage="Authority">Authority</button>
    </div>

    <!-- Pack cards -->
    <div class="pack-grid">
      <?php foreach ($packs as $p):
        $features = website_first(array_values(array_filter(array_map('trim', explode("\n", $p['features']))))); ?>
      <div class="pack-card <?= $p['is_featured'] ? 'featured' : '' ?>" data-stage="<?= e($p['stage']) ?>">
        <?php if ($p['is_featured']): ?><span class="pack-flag">★ Recommended</span><?php endif; ?>
        <a href="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" style="display:block">
          <div class="pack-visual">
            <?php if ($pimg = img_url('packs', $p['image'])): ?>
              <img src="<?= e($pimg) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
            <?php else: ?>
              <i class="bi bi-box-seam"></i>
            <?php endif; ?>
            <span class="pack-stage"><?= e($p['stage']) ?></span>
            <button type="button" class="card-share share-btn" aria-label="Share <?= e($p['name']) ?>" data-share-url="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" data-share-title="<?= e($p['name']) ?>"><i class="bi bi-share-fill"></i></button>
          </div>
        </a>
        <div class="pack-body">
          <a href="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" style="color:inherit">
            <div class="pack-name"><?= e($p['name']) ?></div>
          </a>
          <div class="pack-tagline"><?= e($p['tagline']) ?></div>
          <div class="pack-price"><?= ugx($p['price_ugx']) ?></div>
          <span class="pack-bestfor"><i class="bi bi-people-fill"></i> <?= e($p['best_for']) ?></span>
          <ul class="pack-features">
            <?php foreach ($features as $f): ?>
              <li><i class="bi bi-check-circle-fill"></i> <?= e($f) ?></li>
            <?php endforeach; ?>
          </ul>
          <div class="pack-actions">
            <button class="btn btn-primary" data-add-cart data-type="pack"
                    data-id="<?= (int)$p['id'] ?>" data-name="<?= e($p['name']) ?>"
                    data-price="<?= (int)$p['price_ugx'] ?>" data-icon="bi-box-seam">
              <i class="bi bi-bag-plus"></i> Add to Cart
            </button>
            <a href="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" class="btn btn-ghost btn-sm"><i class="bi bi-box2-heart"></i> See what's inside</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ROI comparison -->
<section class="section bg-soft">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-piggy-bank"></i> Value Stacking</span>
      <h2 class="section-title">Why a pack beats buying piece by piece</h2>
      <p class="section-sub">The Growth Pack alone bundles work that would cost far more if bought separately.</p>
    </div>
    <div class="roi" style="margin-top:26px">
      <div>
        <div style="font-size:13px;color:#9C9CB2;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Buying separately</div>
        <div class="roi-old"><?= ugx(720000) ?></div>
        <div style="font-size:13px;color:#B7B7C9;margin-top:6px">Logo + cards + flyers + profile + website + Google setup, each at shop price.</div>
      </div>
      <div style="text-align:right">
        <div style="font-size:13px;color:#9C9CB2;font-weight:600;text-transform:uppercase;letter-spacing:.06em">Growth Pack</div>
        <div class="roi-new"><?= ugx(500000) ?></div>
        <div style="font-size:13px;color:#B7B7C9;margin-top:6px">You save <strong style="color:#fff"><?= ugx(220000) ?></strong> and get one consistent brand.</div>
      </div>
    </div>
  </div>
</section>

<!-- Add-ons -->
<section class="section">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-plus-square"></i> Add-ons</span>
      <h2 class="section-title">Top up any pack</h2>
    </div>
    <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));margin-top:26px">
      <?php foreach ($addons as [$name, $price, $icon]): ?>
      <div class="card" style="padding:18px;display:flex;align-items:center;gap:14px">
        <span style="width:46px;height:46px;border-radius:12px;background:var(--violet-lt);color:var(--violet-dk);display:grid;place-items:center;font-size:20px;flex-shrink:0"><i class="bi <?= $icon ?>"></i></span>
        <div style="flex:1">
          <div style="font-weight:700;font-size:14.5px"><?= e($name) ?></div>
          <div style="font-family:'Sora';font-weight:800"><?= ugx($price) ?></div>
        </div>
        <a href="<?= wa_link("Hello! I'd like to add: {$name} (".ugx($price).").") ?>" target="_blank" rel="noopener" class="icon-btn" style="background:var(--bg-soft)" aria-label="Add via WhatsApp"><i class="bi bi-whatsapp" style="color:var(--wa)"></i></a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Monthly services -->
<section class="section bg-soft">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-arrow-repeat"></i> Monthly Services</span>
      <h2 class="section-title">Keep growing every month</h2>
    </div>
    <div class="grid grid-2" style="margin-top:26px">
      <div class="card svc-card">
        <div class="svc-ico"><i class="bi bi-instagram"></i></div>
        <h3>Social Media Management</h3>
        <p>We design and schedule posts so your brand stays active and consistent.</p>
        <a href="<?= wa_link('Hi! I want a monthly social media management plan.') ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="margin-top:14px"><i class="bi bi-whatsapp"></i> Get a quote</a>
      </div>
      <div class="card svc-card">
        <div class="svc-ico"><i class="bi bi-wrench-adjustable"></i></div>
        <h3>Website Maintenance</h3>
        <p>Updates, fixes and content changes so your website stays fresh and online.</p>
        <a href="<?= wa_link('Hi! I want a website maintenance plan.') ?>" target="_blank" rel="noopener" class="btn btn-primary btn-sm" style="margin-top:14px"><i class="bi bi-whatsapp"></i> Get a quote</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
