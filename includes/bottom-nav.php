<?php if (!defined('SITE_NAME')) require_once dirname(__DIR__) . '/config.php'; ?>
<!-- ════════════════ MOBILE STICKY BOTTOM NAV ════════════════ -->
<nav class="bottom-nav" aria-label="Mobile navigation">
  <a href="<?= SITE_URL ?>/" class="<?= navActive('index') ?>">
    <i class="bi bi-house-door"></i><span>Home</span>
  </a>
  <a href="<?= SITE_URL ?>/packs.php" class="<?= navActive('packs') ?>">
    <i class="bi bi-box-seam"></i><span>Packs</span>
  </a>
  <a href="<?= SITE_URL ?>/shop.php" class="<?= navActive('shop') ?>">
    <i class="bi bi-bag"></i><span>Shop</span>
  </a>
  <button type="button" id="chatOpenBtnBottom">
    <i class="bi bi-chat-dots"></i><span>Chat</span>
  </button>
  <a href="<?= wa_link('Hello Cheapa Studio!') ?>" target="_blank" rel="noopener" class="bn-wa">
    <i class="bi bi-whatsapp"></i><span>WhatsApp</span>
  </a>
</nav>
