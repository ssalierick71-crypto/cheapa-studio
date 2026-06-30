<?php
require_once 'config.php';
require_once 'includes/db.php';

$pageTitle = 'Contact';
$pageDesc  = 'Get in touch with Cheapa Studio. Tell us what you need and we will reply on WhatsApp.';

$prefillService = trim($_GET['service'] ?? '');

$ok = false; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $whatsapp = trim($_POST['whatsapp'] ?? '');
    if ($whatsapp === '') {
        $err = 'Please enter your WhatsApp number so we can reach you.';
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO leads (name, whatsapp, business_name, service_type, budget, message, source)
             VALUES (?, ?, ?, ?, ?, ?, 'contact')"
        );
        $stmt->execute([
            trim($_POST['name'] ?? ''),
            $whatsapp,
            trim($_POST['business_name'] ?? ''),
            trim($_POST['service_type'] ?? ''),
            trim($_POST['budget'] ?? ''),
            trim($_POST['message'] ?? ''),
        ]);
        $ok = true;
    }
}

$serviceOptions = ['Business Growth Pack', 'Design Shop Order', 'Web Design', 'Brand Identity', 'Print Design', 'Social Media', 'Not sure yet'];

include 'includes/header.php';
?>

<section class="hero" style="padding:44px 0 36px">
  <div class="container hero-inner">
    <span class="eyebrow"><i class="bi bi-chat-dots"></i> Let's Talk</span>
    <h1 style="font-size:clamp(1.7rem,6vw,2.6rem)">Start your project</h1>
    <p class="hero-sub">Fill the form or message us directly. The only thing we really need is your WhatsApp number.</p>
  </div>
</section>

<section class="section">
  <div class="container" style="display:grid;grid-template-columns:1fr;gap:24px;max-width:720px">

    <div class="form-card" style="max-width:none">
      <?php if ($ok): ?>
        <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>
          <div>
            <strong>Thank you!</strong> Your request has been received.
            <div style="font-weight:500;margin-top:6px">Next steps: we'll review your request and reach out on WhatsApp to confirm details and the 50% deposit to begin.</div>
            <a href="<?= wa_link('Hi! I just submitted a request on your website.') ?>" target="_blank" rel="noopener" class="btn btn-wa btn-sm" style="margin-top:12px"><i class="bi bi-whatsapp"></i> Message us now</a>
          </div>
        </div>
      <?php elseif ($err): ?>
        <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= e($err) ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="form-row">
          <div class="form-group">
            <label>Your name</label>
            <input type="text" name="name" class="form-control" placeholder="Optional" value="<?= e($_POST['name'] ?? '') ?>">
          </div>
          <div class="form-group">
            <label>Business name</label>
            <input type="text" name="business_name" class="form-control" placeholder="Optional" value="<?= e($_POST['business_name'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label>What do you need?</label>
          <div class="svc-pills">
            <?php foreach ($serviceOptions as $i => $opt):
              $checked = ($prefillService && stripos($opt, 'Pack') !== false && stripos($prefillService, 'Pack') !== false) ? 'checked' : ''; ?>
            <div class="svc-pill">
              <input type="radio" name="service_type" id="svc<?= $i ?>" value="<?= e($opt) ?>" <?= $checked ?>>
              <label for="svc<?= $i ?>"><i class="bi bi-check-circle"></i> <?= e($opt) ?></label>
            </div>
            <?php endforeach; ?>
          </div>
          <?php if ($prefillService): ?><div class="form-hint">Pre-filled from: <?= e($prefillService) ?></div><?php endif; ?>
        </div>

        <div class="form-group">
          <label>Budget range</label>
          <select name="budget" class="form-control">
            <option value="">Select…</option>
            <option>Under UGX 150,000</option>
            <option>UGX 150,000 – 500,000</option>
            <option>UGX 500,000 – 1,000,000</option>
            <option>Over UGX 1,000,000</option>
          </select>
        </div>

        <div class="form-group">
          <label>Describe your project</label>
          <textarea name="message" class="form-control" placeholder="Tell us what you're looking for…"><?= e($_POST['message'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label>WhatsApp number <span class="req">*</span></label>
          <input type="text" name="whatsapp" class="form-control" required placeholder="e.g. 0700 000000" value="<?= e($_POST['whatsapp'] ?? '') ?>">
          <div class="form-hint">Required so we can reply and send next steps.</div>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg"><i class="bi bi-send"></i> Send Request</button>
      </form>
    </div>

    <div class="card" style="padding:22px">
      <h3 style="font-size:1.1rem;margin-bottom:14px">Or reach us directly</h3>
      <div class="footer-contacts" style="margin-top:0">
        <a href="<?= wa_link('Hello Cheapa Studio!') ?>" target="_blank" rel="noopener" style="color:var(--text);font-weight:600"><i class="bi bi-whatsapp" style="color:var(--wa)"></i> +<?= e(preg_replace('/\D/', '', cfg('whatsapp_number'))) ?></a>
        <a href="tel:<?= preg_replace('/\s+/', '', cfg('phone_1')) ?>" style="color:var(--text);font-weight:600"><i class="bi bi-telephone" style="color:var(--violet)"></i> <?= e(cfg('phone_1')) ?></a>
        <a href="mailto:<?= e(cfg('email')) ?>" style="color:var(--text);font-weight:600"><i class="bi bi-envelope" style="color:var(--violet)"></i> <?= e(cfg('email')) ?></a>
        <span style="color:var(--text);font-weight:600"><i class="bi bi-geo-alt" style="color:var(--violet)"></i> <?= e(cfg('location')) ?></span>
      </div>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>
