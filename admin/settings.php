<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$flashError = '';
$settingKeys = ['whatsapp_number','phone_1','email','location','site_tagline'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $flashError = 'Security check failed. Please try again.';
    } elseif (($_POST['action'] ?? '') === 'contact') {
        foreach ($settingKeys as $k) {
            setting_upsert($pdo, $k, trim($_POST[$k] ?? ''));
        }
        // logo upload / removal
        if (!empty($_POST['remove_logo'])) {
            delete_image('brand', cfg('logo', ''));
            setting_upsert($pdo, 'logo', '');
        } else {
            $newLogo = upload_image('logo', 'brand', cfg('logo', '') ?: null);
            if ($newLogo) setting_upsert($pdo, 'logo', $newLogo);
        }
        header('Location: ' . SITE_URL . '/admin/settings.php?ok=' . rawurlencode('Settings saved'));
        exit;
    } elseif (($_POST['action'] ?? '') === 'password') {
        $cur = $_POST['current'] ?? '';
        $new = $_POST['new'] ?? '';
        $row = $pdo->query("SELECT * FROM admin_users WHERE id = " . (int)$_SESSION['admin_id'])->fetch();
        if (!$row || $row['password'] === '' || !password_verify($cur, $row['password'])) {
            $flashError = 'Current password is incorrect.';
        } elseif (strlen($new) < 6) {
            $flashError = 'New password must be at least 6 characters.';
        } else {
            $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?")
                ->execute([password_hash($new, PASSWORD_DEFAULT), (int)$_SESSION['admin_id']]);
            header('Location: ' . SITE_URL . '/admin/settings.php?ok=' . rawurlencode('Password changed'));
            exit;
        }
    }
}

// current values (DB first, then constant fallback via cfg())
$val = [];
foreach ($settingKeys as $k) $val[$k] = cfg($k);

$adminTitle = 'Settings';
require_once dirname(__DIR__) . '/includes/admin-header.php';
?>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;align-items:start">

  <div class="admin-card">
    <h2 style="font-size:1.1rem;margin-bottom:4px">Contact &amp; brand</h2>
    <p style="color:var(--text-faint);font-size:13px;margin-bottom:16px">These drive every WhatsApp button and footer on the public site.</p>
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="contact">
      <div class="form-group">
        <label>Logo</label>
        <?php if ($logo = cfg('logo', '')): ?>
          <div style="margin-bottom:8px"><img src="<?= UPLOADS_URL ?>brand/<?= e($logo) ?>" alt="logo" style="height:50px;background:var(--ink);padding:8px 12px;border-radius:10px"></div>
        <?php endif; ?>
        <input type="file" name="logo" accept="image/*" class="form-control">
        <?php if (cfg('logo', '')): ?><label style="font-size:13px;margin-top:8px;display:flex;gap:6px;align-items:center"><input type="checkbox" name="remove_logo"> Remove logo (use the built-in wordmark)</label><?php endif; ?>
        <div class="form-hint">Best: a transparent PNG with a white/light logo — it shows in the footer, login and admin sidebar.</div>
      </div>
      <div class="form-group"><label>WhatsApp number <span style="font-weight:400;color:var(--text-faint)">(digits only, with country code)</span></label><input name="whatsapp_number" class="form-control" value="<?= e($val['whatsapp_number']) ?>" placeholder="256700000000"></div>
      <div class="form-group"><label>Phone (display)</label><input name="phone_1" class="form-control" value="<?= e($val['phone_1']) ?>"></div>
      <div class="form-group"><label>Email</label><input name="email" class="form-control" value="<?= e($val['email']) ?>"></div>
      <div class="form-group"><label>Location</label><input name="location" class="form-control" value="<?= e($val['location']) ?>"></div>
      <div class="form-group"><label>Tagline</label><input name="site_tagline" class="form-control" value="<?= e($val['site_tagline']) ?>"></div>
      <button class="btn btn-primary"><i class="bi bi-check2"></i> Save settings</button>
    </form>
  </div>

  <div class="admin-card">
    <h2 style="font-size:1.1rem;margin-bottom:16px">Change password</h2>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="password">
      <div class="form-group"><label>Current password</label><input type="password" name="current" class="form-control" required></div>
      <div class="form-group"><label>New password</label><input type="password" name="new" class="form-control" required minlength="6"></div>
      <button class="btn btn-dark"><i class="bi bi-shield-lock"></i> Update password</button>
    </form>
  </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
