<?php
require_once 'config.php';
require_once 'includes/db.php';

$pageTitle = 'Web Design & Custom Services';
$pageDesc  = 'Custom websites, landing pages, brand identity, print and social media design for businesses in Uganda.';

$ok = false; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    if ($whatsapp === '') {
        $err = 'Please enter your WhatsApp number so we can reach you.';
    } else {
        $details = [];
        if (!empty($_POST['business_type'])) $details[] = 'Type: ' . $_POST['business_type'];
        if (!empty($_POST['pages']))         $details[] = 'Pages: ' . $_POST['pages'];
        if (!empty($_POST['deadline']))      $details[] = 'Deadline: ' . $_POST['deadline'];
        if (!empty($_POST['references']))    $details[] = 'References: ' . $_POST['references'];
        $message = trim(implode("\n", $details));

        $stmt = $pdo->prepare(
            "INSERT INTO leads (name, whatsapp, business_name, service_type, budget, message, source)
             VALUES (?, ?, ?, 'Web Design', ?, ?, 'services')"
        );
        $stmt->execute([
            trim($_POST['name'] ?? ''),
            $whatsapp,
            trim($_POST['business_name'] ?? ''),
            trim($_POST['budget'] ?? ''),
            $message,
        ]);
        $ok = true;
    }
}

$services = [
  ['bi-globe2', 'Web Design', 'Our primary service.', ['Business websites', 'Landing pages', 'Portfolio sites', 'Website redesign']],
  ['bi-vector-pen', 'Brand Identity', 'Look the part everywhere.', ['Logos', 'Brand systems', 'Business cards', 'Letterheads']],
  ['bi-printer', 'Print Design', 'Ready for the printer.', ['Flyers', 'Posters', 'Banners', 'Receipt books']],
  ['bi-instagram', 'Social Media Design', 'Show up consistently.', ['Posts', 'Campaign creatives', 'WhatsApp branding']],
];

include 'includes/header.php';
?>

<section class="hero" style="padding:46px 0 40px">
  <div class="container hero-inner">
    <span class="eyebrow"><i class="bi bi-globe2"></i> Custom Work Hub</span>
    <h1 style="font-size:clamp(1.7rem,6vw,2.7rem)">Websites & custom design,<br><span class="grad-text">built for your business</span></h1>
    <p class="hero-sub">Mobile-friendly websites and tailored branding — high-value work, done affordably.</p>
    <div class="hero-ctas">
      <a href="#web-form" class="btn btn-primary btn-lg"><i class="bi bi-window"></i> Request a Website</a>
      <a href="<?= wa_link('Hi! I want to discuss a custom design project.') ?>" target="_blank" rel="noopener" class="btn btn-light btn-lg"><i class="bi bi-whatsapp"></i> WhatsApp Us</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2">
      <?php foreach ($services as [$icon, $title, $sub, $items]): ?>
      <div class="card svc-card">
        <div class="svc-ico"><i class="bi <?= $icon ?>"></i></div>
        <h3><?= e($title) ?></h3>
        <p><?= e($sub) ?></p>
        <ul>
          <?php foreach ($items as $it): ?><li><i class="bi bi-check2"></i> <?= e($it) ?></li><?php endforeach; ?>
        </ul>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Web design request form -->
<section class="section bg-soft" id="web-form">
  <div class="container">
    <div class="text-center">
      <span class="eyebrow"><i class="bi bi-window-stack"></i> Web Design Request</span>
      <h2 class="section-title">Tell us about your website</h2>
      <p class="section-sub">All fields are optional except your WhatsApp number. We'll review and reply with next steps.</p>
    </div>

    <div class="form-card" style="margin-top:26px">
      <?php if ($ok): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>
          <div><strong>Request received!</strong> We'll contact you on WhatsApp shortly. Want to talk now?
            <a href="<?= wa_link('Hi! I just submitted a web design request.') ?>" target="_blank" rel="noopener" style="color:var(--violet-dk);font-weight:700">Message us →</a>
          </div>
        </div>
      <?php elseif ($err): ?>
        <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= e($err) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="form-row">
          <div class="form-group">
            <label>Business name</label>
            <input type="text" name="business_name" class="form-control" placeholder="e.g. Glow Salon">
          </div>
          <div class="form-group">
            <label>Business type</label>
            <input type="text" name="business_type" class="form-control" placeholder="e.g. Salon, Shop, Clinic">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Budget range</label>
            <select name="budget" class="form-control">
              <option value="">Select…</option>
              <option>Under UGX 300,000</option>
              <option>UGX 300,000 – 600,000</option>
              <option>UGX 600,000 – 1,000,000</option>
              <option>Over UGX 1,000,000</option>
            </select>
          </div>
          <div class="form-group">
            <label>Pages needed</label>
            <input type="text" name="pages" class="form-control" placeholder="e.g. Home, About, Services, Contact">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Deadline</label>
            <input type="text" name="deadline" class="form-control" placeholder="e.g. In 2 weeks">
          </div>
          <div class="form-group">
            <label>Your name</label>
            <input type="text" name="name" class="form-control" placeholder="Optional">
          </div>
        </div>
        <div class="form-group">
          <label>Reference links</label>
          <input type="text" name="references" class="form-control" placeholder="Websites you like (optional)">
        </div>
        <div class="form-group">
          <label>WhatsApp number <span class="req">*</span></label>
          <input type="text" name="whatsapp" class="form-control" required placeholder="e.g. 0700 000000" value="<?= e($_POST['whatsapp'] ?? '') ?>">
          <div class="form-hint">The only required field — so we can reach you.</div>
        </div>
        <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="bi bi-send"></i> Submit Request</button>
      </form>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
