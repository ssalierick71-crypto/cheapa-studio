<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: ' . SITE_URL . '/admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
    $stmt->execute([$user]);
    $admin = $stmt->fetch();

    if ($admin && $admin['password'] !== '' && password_verify($pass, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: ' . SITE_URL . '/admin/index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — <?= SITE_NAME ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-card">
    <div class="lc-logo" style="display:flex;justify-content:center;margin-bottom:6px"><?= brand_logo('light') ?></div>
    <div class="lc-sub">Admin Panel</div>
    <h2>Sign In</h2>

    <?php if ($error): ?>
      <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="on">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="admin" required autocomplete="username" value="<?= e($_POST['username'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="pw-wrap">
          <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password" style="padding-right:40px">
          <button type="button" class="pw-toggle"><i class="bi bi-eye"></i></button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block"><i class="bi bi-box-arrow-in-right"></i> Sign In</button>
    </form>

    <p style="text-align:center;margin-top:18px;font-size:13px">
      <a href="<?= SITE_URL ?>/" style="color:var(--violet-dk)">← Back to website</a>
    </p>
  </div>
</div>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>
