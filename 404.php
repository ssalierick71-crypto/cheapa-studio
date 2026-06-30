<?php
require_once 'config.php';
http_response_code(404);
$pageTitle = 'Page Not Found';
include 'includes/header.php';
?>
<section class="section" style="text-align:center;padding:80px 0">
  <div class="container">
    <div style="font-family:'Sora';font-weight:800;font-size:5rem;background:var(--grad);-webkit-background-clip:text;background-clip:text;color:transparent">404</div>
    <h1 class="section-title" style="margin-top:6px">Page not found</h1>
    <p class="section-sub">The page you're looking for moved or never existed. Let's get you back on track.</p>
    <div class="hero-ctas" style="margin-top:22px">
      <a href="<?= SITE_URL ?>/" class="btn btn-primary"><i class="bi bi-house-door"></i> Go Home</a>
      <a href="<?= SITE_URL ?>/packs.php" class="btn btn-ghost">View Packs</a>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>
