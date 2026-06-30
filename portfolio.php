<?php
require_once 'config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Portfolio';
$pageDesc  = 'Real before-and-after brand transformations for salons, shops, restaurants, clinics and schools in Uganda.';

$cases = $pdo->query("SELECT * FROM portfolio WHERE is_active=1 ORDER BY sort_order ASC")->fetchAll();

include 'includes/header.php';
?>

<section class="hero" style="padding:46px 0 40px">
  <div class="container hero-inner">
    <span class="eyebrow"><i class="bi bi-images"></i> Proof of Results</span>
    <h1 style="font-size:clamp(1.7rem,6vw,2.7rem)">Real businesses,<br><span class="grad-text">real transformations</span></h1>
    <p class="hero-sub">Not just pretty pictures — each project solved a problem and delivered a result.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2">
      <?php foreach ($cases as $c): ?>
      <div class="case-card">
        <div class="case-ba">
          <div class="case-before"><span class="ba-tag">Before</span>
            <?php if ($bi = img_url('portfolio', $c['before_image'])): ?><img class="zoomable" src="<?= e($bi) ?>" alt="<?= e($c['title']) ?> — before" loading="lazy"><?php else: ?><i class="bi bi-emoji-frown" style="font-size:34px"></i><?php endif; ?>
          </div>
          <div class="case-after"><span class="ba-tag">After</span>
            <?php if ($ai = img_url('portfolio', $c['after_image'])): ?><img class="zoomable" src="<?= e($ai) ?>" alt="<?= e($c['title']) ?> — after" loading="lazy"><?php else: ?><i class="bi bi-emoji-sunglasses" style="font-size:34px"></i><?php endif; ?>
          </div>
        </div>
        <div class="case-body">
          <span class="case-industry"><?= e($c['industry']) ?></span>
          <div class="case-title"><?= e($c['title']) ?></div>
          <div class="case-psr">
            <div><b>Problem</b> <span><?= e($c['problem']) ?></span></div>
            <div><b>Solution</b> <span><?= e($c['solution']) ?></span></div>
            <div><b>Result</b> <span><?= e($c['result']) ?></span></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center" style="margin-top:34px">
      <h2 class="section-title" style="font-size:1.5rem">Want results like these?</h2>
      <div class="hero-ctas" style="margin-top:18px">
        <a href="<?= SITE_URL ?>/packs.php" class="btn btn-primary"><i class="bi bi-box-seam"></i> Choose a Pack</a>
        <a href="<?= wa_link('Hi! I saw your portfolio and want similar results for my business.') ?>" target="_blank" rel="noopener" class="btn btn-wa"><i class="bi bi-whatsapp"></i> WhatsApp Us</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
