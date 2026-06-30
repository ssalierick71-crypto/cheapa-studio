<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Professional Branding Made Affordable';
$pageDesc  = 'Logos, flyers, banners, business cards, websites and complete brand identity for growing businesses in Kampala, Uganda.';

$featuredPacks = $pdo->query("SELECT * FROM packs WHERE is_active=1 ORDER BY is_featured DESC, sort_order ASC LIMIT 4")->fetchAll();
$cases         = $pdo->query("SELECT * FROM portfolio WHERE is_active=1 ORDER BY sort_order ASC LIMIT 4")->fetchAll();

include 'includes/header.php';
?>

<!-- ════════════════ HERO ════════════════ -->
<section class="hero">
  <div class="container hero-inner">
    <span class="eyebrow"><i class="bi bi-stars"></i> Kampala Creative Agency</span>
    <h1>Professional Branding<br><span class="grad-text">Made Affordable</span></h1>
    <p class="hero-sub">Logos, flyers, banners, business cards, websites and complete brand identity for growing businesses in Uganda.</p>
    <div class="hero-ctas">
      <a href="<?= SITE_URL ?>/packs.php" class="btn btn-primary btn-lg"><i class="bi bi-box-seam"></i> View Packs</a>
      <a href="<?= SITE_URL ?>/shop.php" class="btn btn-light btn-lg"><i class="bi bi-bag"></i> Browse Shop</a>
    </div>

    <div class="hero-stats">
      <div class="hero-stat"><strong>100+</strong><span>Brands launched</span></div>
      <div class="hero-stat"><strong>48hr</strong><span>Fast delivery</span></div>
      <div class="hero-stat"><strong>4.9★</strong><span>Client rating</span></div>
    </div>

    <!-- Quick action selector -->
    <div class="quick-actions">
      <a href="<?= SITE_URL ?>/packs.php" class="qa-card">
        <span class="qa-ico"><i class="bi bi-box-seam"></i></span>
        <span><b>Business Growth Packs</b><small>Bundles that save you money</small></span>
      </a>
      <a href="<?= SITE_URL ?>/shop.php" class="qa-card">
        <span class="qa-ico"><i class="bi bi-bag-heart"></i></span>
        <span><b>Design Shop</b><small>Order individual designs</small></span>
      </a>
      <a href="<?= SITE_URL ?>/services.php" class="qa-card">
        <span class="qa-ico"><i class="bi bi-globe2"></i></span>
        <span><b>Web Design Services</b><small>Websites & custom work</small></span>
      </a>
      <button class="qa-card" id="chatOpenHomeQA" type="button">
        <span class="qa-ico"><i class="bi bi-question-lg"></i></span>
        <span><b>I'm not sure</b><small>Let us guide you</small></span>
      </button>
    </div>
  </div>
</section>

<!-- ════════════════ TRUST ════════════════ -->
<section class="section-sm">
  <div class="container">
    <div class="trust-grid">
      <div class="trust-item"><i class="bi bi-patch-check-fill"></i> Uganda-based agency</div>
      <div class="trust-item"><i class="bi bi-lightning-charge-fill"></i> Fast delivery</div>
      <div class="trust-item"><i class="bi bi-printer-fill"></i> Print + digital</div>
      <div class="trust-item"><i class="bi bi-phone-fill"></i> Mobile websites</div>
      <div class="trust-item"><i class="bi bi-cash-coin"></i> Affordable</div>
    </div>
  </div>
</section>

<!-- ════════════════ FEATURED PACKS ════════════════ -->
<section class="section bg-soft">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-box-seam"></i> Most Popular</span>
      <h2 class="section-title">Business Growth Packs</h2>
      <p class="section-sub">Pre-built bundles that give Ugandan businesses everything they need to look professional — for less.</p>
    </div>
    <div class="pack-grid" style="margin-top:30px">
      <?php foreach ($featuredPacks as $p):
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
            <?php foreach (array_slice($features, 0, 5) as $f): ?>
              <li><i class="bi bi-check-circle-fill"></i> <?= e($f) ?></li>
            <?php endforeach; ?>
            <?php if (count($features) > 5): ?><li><i class="bi bi-plus-circle-fill"></i> + <?= count($features) - 5 ?> more</li><?php endif; ?>
          </ul>
          <div class="pack-actions">
            <a href="<?= SITE_URL ?>/pack.php?slug=<?= e($p['slug']) ?>" class="btn btn-primary"><i class="bi bi-box2-heart"></i> See what's inside</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center" style="margin-top:24px">
      <a href="<?= SITE_URL ?>/packs.php" class="btn btn-ghost">See all packs & add-ons <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- ════════════════ HOW IT WORKS ════════════════ -->
<section class="section">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-diagram-3"></i> Simple Process</span>
      <h2 class="section-title">How It Works</h2>
    </div>
    <div class="steps" style="margin-top:28px">
      <div class="step"><b>Choose</b><p>Pick a pack, product or service.</p></div>
      <div class="step"><b>Submit</b><p>Send your request & details.</p></div>
      <div class="step"><b>Deposit</b><p>Pay 50% to begin.</p></div>
      <div class="step"><b>Design</b><p>We create your work.</p></div>
      <div class="step"><b>Review</b><p>Revise until you're happy.</p></div>
      <div class="step"><b>Deliver</b><p>Final files after approval.</p></div>
    </div>
  </div>
</section>

<!-- ════════════════ PORTFOLIO PREVIEW ════════════════ -->
<section class="section bg-soft">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-images"></i> Real Results</span>
      <h2 class="section-title">Transformations</h2>
      <p class="section-sub">Before → after. Real Ugandan businesses we've helped look the part.</p>
    </div>
  </div>
  <div class="container">
    <div class="swipe-rail" style="margin-top:26px">
      <?php foreach ($cases as $c): ?>
      <div class="case-card" style="width:300px">
        <div class="case-ba">
          <div class="case-before"><span class="ba-tag">Before</span>
            <?php if ($bi = img_url('portfolio', $c['before_image'])): ?><img src="<?= e($bi) ?>" alt="Before" loading="lazy"><?php else: ?><i class="bi bi-emoji-frown" style="font-size:30px"></i><?php endif; ?>
          </div>
          <div class="case-after"><span class="ba-tag">After</span>
            <?php if ($ai = img_url('portfolio', $c['after_image'])): ?><img src="<?= e($ai) ?>" alt="After" loading="lazy"><?php else: ?><i class="bi bi-emoji-sunglasses" style="font-size:30px"></i><?php endif; ?>
          </div>
        </div>
        <div class="case-body">
          <span class="case-industry"><?= e($c['industry']) ?></span>
          <div class="case-title"><?= e($c['title']) ?></div>
          <div class="case-psr"><div><b>Result</b> <span><?= e($c['result']) ?></span></div></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="container text-center" style="margin-top:8px">
      <a href="<?= SITE_URL ?>/portfolio.php" class="btn btn-ghost">View full portfolio <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>

<!-- ════════════════ FINAL CTA ════════════════ -->
<section class="section">
  <div class="container">
    <div class="bg-ink" style="border-radius:var(--r-xl);padding:44px 26px;text-align:center">
      <span class="eyebrow"><i class="bi bi-rocket-takeoff"></i> Let's Build</span>
      <h2 class="section-title" style="color:#fff">Ready to grow your brand?</h2>
      <p class="section-sub">Start a project today — we respond fast on WhatsApp.</p>
      <div class="hero-ctas" style="margin-top:22px">
        <a href="<?= wa_link('Hello Cheapa Studio! I would like to start a project.') ?>" target="_blank" rel="noopener" class="btn btn-wa btn-lg"><i class="bi bi-whatsapp"></i> WhatsApp Us</a>
        <a href="<?= SITE_URL ?>/contact.php" class="btn btn-light btn-lg"><i class="bi bi-send"></i> Start a Project</a>
      </div>
    </div>
  </div>
</section>

<script>
  document.getElementById('chatOpenHomeQA').addEventListener('click', function(){
    document.getElementById('chatFab').click();
  });
</script>

<?php include 'includes/footer.php'; ?>
