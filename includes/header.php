<?php
if (!defined('SITE_NAME')) { require_once dirname(__DIR__) . '/config.php'; }
// log this public page view (no-op if the DB isn't loaded, e.g. 404)
require_once __DIR__ . '/track.php';
track_visit();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
function navActive(string $page): string {
    global $currentPage;
    return $currentPage === $page ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#0A1A40">
<?php
  $metaTitle = (isset($pageTitle) ? $pageTitle . ' — ' : '') . SITE_NAME;
  $metaDesc  = isset($pageDesc) ? $pageDesc : SITE_NAME . ' — affordable, professional branding, websites and design for growing businesses in ' . cfg('location') . '.';
  $metaImg   = $ogImage ?? (SITE_URL . '/uploads/packs/growth-pack.jpg');
?>
<title><?= e($metaTitle) ?></title>
<meta name="description" content="<?= e($metaDesc) ?>">
<!-- Open Graph / social share preview -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:title" content="<?= e($pageTitle ?? SITE_NAME) ?>">
<meta property="og:description" content="<?= e($metaDesc) ?>">
<meta property="og:url" content="<?= e(current_url()) ?>">
<meta property="og:image" content="<?= e($metaImg) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($pageTitle ?? SITE_NAME) ?>">
<meta name="twitter:description" content="<?= e($metaDesc) ?>">
<meta name="twitter:image" content="<?= e($metaImg) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
<style>
#preloader{position:fixed;inset:0;z-index:9999;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:24px;transition:opacity .45s ease,visibility .45s ease}
#preloader.hidden{opacity:0;visibility:hidden}
#preloader .pl-logo{width:150px;max-width:52vw;opacity:.95}
#preloader .pl-word{font-family:'Sora',sans-serif;font-weight:800;font-size:30px;color:#0A1A40}
#preloader .pl-sk{width:230px;max-width:62vw;display:flex;flex-direction:column;gap:11px}
#preloader .pl-bar{height:13px;border-radius:8px;background:linear-gradient(90deg,#eef1f7 25%,#dfe6f3 37%,#eef1f7 63%);background-size:400% 100%;animation:plShimmer 1.3s ease infinite}
#preloader .pl-bar:nth-child(2){width:78%}
#preloader .pl-bar:nth-child(3){width:55%}
@keyframes plShimmer{0%{background-position:100% 0}100%{background-position:0 0}}
</style>
<noscript><style>#preloader{display:none}</style></noscript>
<script>
document.addEventListener('DOMContentLoaded',function(){var p=document.getElementById('preloader');if(!p)return;setTimeout(function(){p.classList.add('hidden');setTimeout(function(){if(p.parentNode)p.parentNode.removeChild(p);},500);},180);});
setTimeout(function(){var p=document.getElementById('preloader');if(p)p.classList.add('hidden');},6000);
</script>
</head>
<body data-page="<?= e($currentPage) ?>">

<!-- Skeleton preloader (hides as soon as the page is ready) -->
<div id="preloader" aria-hidden="true">
  <?php if ($plLogo = brand_logo_src('light')): ?>
    <img class="pl-logo" src="<?= e($plLogo) ?>" alt="<?= e(SITE_NAME) ?>">
  <?php else: ?>
    <div class="pl-word">Cheapa</div>
  <?php endif; ?>
  <div class="pl-sk"><div class="pl-bar"></div><div class="pl-bar"></div><div class="pl-bar"></div></div>
</div>

<div id="page-wrap">

<!-- ════════════════════════ TOP NAVBAR ════════════════════════ -->
<header class="site-navbar">
  <div class="navbar-inner">

    <a href="<?= SITE_URL ?>/" class="navbar-logo" aria-label="<?= SITE_NAME ?> home"><?= brand_logo('light') ?></a>

    <nav class="navbar-links" aria-label="Primary">
      <a href="<?= SITE_URL ?>/"             class="<?= navActive('index') ?>">Home</a>
      <a href="<?= SITE_URL ?>/packs.php"     class="<?= navActive('packs') ?>">Packs</a>
      <a href="<?= SITE_URL ?>/shop.php"      class="<?= navActive('shop') ?>">Shop</a>
      <a href="<?= SITE_URL ?>/services.php"  class="<?= navActive('services') ?>">Services</a>
      <a href="<?= SITE_URL ?>/portfolio.php" class="<?= navActive('portfolio') ?>">Portfolio</a>
      <a href="<?= SITE_URL ?>/contact.php"   class="<?= navActive('contact') ?>">Contact</a>
    </nav>

    <div class="navbar-right">
      <button class="cart-btn" id="cartOpenBtn" aria-label="Open cart">
        <i class="bi bi-bag"></i>
        <span class="cart-count" id="cartCount" hidden>0</span>
      </button>
      <a href="<?= wa_link('Hello Cheapa Studio! I found you online and want to know more.') ?>"
         target="_blank" rel="noopener" class="btn btn-wa nav-wa">
        <i class="bi bi-whatsapp"></i><span>WhatsApp</span>
      </a>
      <button class="hamburger" id="hamburgerBtn" aria-label="Open menu" aria-expanded="false">
        <span></span><span></span><span></span>
      </button>
    </div>

  </div>
</header>

<!-- ════════════════════════ MOBILE DRAWER ════════════════════════ -->
<div class="mobile-menu" id="mobileMenu" role="dialog" aria-modal="true" aria-label="Menu">
  <div class="mobile-menu-backdrop" data-close-menu></div>
  <div class="mobile-menu-drawer">
    <div class="mobile-menu-head">
      <span class="mm-brand"><?= brand_logo('light') ?></span>
      <button class="icon-btn" data-close-menu aria-label="Close menu"><i class="bi bi-x-lg"></i></button>
    </div>
    <nav class="mobile-nav-links">
      <a href="<?= SITE_URL ?>/"             class="<?= navActive('index') ?>"><i class="bi bi-house-door"></i> Home</a>
      <a href="<?= SITE_URL ?>/packs.php"     class="<?= navActive('packs') ?>"><i class="bi bi-box-seam"></i> Business Growth Packs</a>
      <a href="<?= SITE_URL ?>/shop.php"      class="<?= navActive('shop') ?>"><i class="bi bi-bag"></i> Design Shop</a>
      <a href="<?= SITE_URL ?>/services.php"  class="<?= navActive('services') ?>"><i class="bi bi-globe2"></i> Web & Custom Services</a>
      <a href="<?= SITE_URL ?>/portfolio.php" class="<?= navActive('portfolio') ?>"><i class="bi bi-collection"></i> Portfolio</a>
      <a href="<?= SITE_URL ?>/contact.php"   class="<?= navActive('contact') ?>"><i class="bi bi-chat-dots"></i> Contact</a>
    </nav>
    <div class="mobile-menu-foot">
      <a href="<?= wa_link('Hello Cheapa Studio!') ?>" target="_blank" rel="noopener" class="btn btn-wa btn-block">
        <i class="bi bi-whatsapp"></i> Chat on WhatsApp
      </a>
    </div>
  </div>
</div>

<main id="main">
