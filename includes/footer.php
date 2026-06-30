<?php if (!defined('SITE_NAME')) require_once dirname(__DIR__) . '/config.php'; ?>
</main><!-- /#main -->

<!-- ════════════════════════ FOOTER ════════════════════════ -->
<footer class="site-footer">
  <div class="footer-cta">
    <div class="footer-cta-inner">
      <div>
        <h3>Ready to grow your brand?</h3>
        <p>Pick a pack, shop a design, or message us — we reply fast.</p>
      </div>
      <div class="footer-cta-btns">
        <a href="<?= SITE_URL ?>/packs.php" class="btn btn-primary"><i class="bi bi-box-seam"></i> View Packs</a>
        <a href="<?= wa_link('Hello Cheapa Studio! I would like to start a project.') ?>" target="_blank" rel="noopener" class="btn btn-wa"><i class="bi bi-whatsapp"></i> WhatsApp Us</a>
      </div>
    </div>
  </div>

  <div class="footer-body">
    <div class="footer-grid">
      <div class="footer-brand-col">
        <div class="footer-logo"><?= brand_logo('dark') ?></div>
        <p class="footer-desc"><?= e(cfg('site_tagline')) ?>. Logos, flyers, banners, business cards, websites and complete brand identity for growing businesses in <?= e(cfg('location')) ?>.</p>
        <div class="footer-contacts">
          <a href="tel:<?= preg_replace('/\s+/', '', cfg('phone_1')) ?>"><i class="bi bi-telephone"></i> <?= e(cfg('phone_1')) ?></a>
          <a href="<?= wa_link() ?>" target="_blank" rel="noopener"><i class="bi bi-whatsapp"></i> +<?= e(preg_replace('/\D/', '', cfg('whatsapp_number'))) ?></a>
          <a href="mailto:<?= e(cfg('email')) ?>"><i class="bi bi-envelope"></i> <?= e(cfg('email')) ?></a>
          <span><i class="bi bi-geo-alt"></i> <?= e(cfg('location')) ?></span>
        </div>
      </div>
      <div class="footer-col">
        <h4>Explore</h4>
        <ul>
          <li><a href="<?= SITE_URL ?>/packs.php">Business Growth Packs</a></li>
          <li><a href="<?= SITE_URL ?>/shop.php">Design Shop</a></li>
          <li><a href="<?= SITE_URL ?>/services.php">Web Design</a></li>
          <li><a href="<?= SITE_URL ?>/portfolio.php">Portfolio</a></li>
          <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Why Cheapa Studio</h4>
        <ul class="footer-trust">
          <li><i class="bi bi-patch-check-fill"></i> Uganda-based agency</li>
          <li><i class="bi bi-lightning-charge-fill"></i> Fast delivery</li>
          <li><i class="bi bi-printer-fill"></i> Print + digital design</li>
          <li><i class="bi bi-phone-fill"></i> Mobile-friendly websites</li>
          <li><i class="bi bi-cash-coin"></i> Affordable solutions</li>
        </ul>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <span>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</span>
    <span class="footer-bottom-links">
      <a href="<?= SITE_URL ?>/">Home</a> ·
      <a href="<?= SITE_URL ?>/packs.php">Packs</a> ·
      <a href="<?= SITE_URL ?>/contact.php">Contact</a>
    </span>
  </div>
</footer>

<?php include __DIR__ . '/cart-drawer.php'; ?>
<?php include __DIR__ . '/chatbot.php'; ?>
<?php include __DIR__ . '/bottom-nav.php'; ?>

</div><!-- /#page-wrap -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
