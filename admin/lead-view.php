<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
$stmt->execute([$id]);
$lead = $stmt->fetch();
if (!$lead) { header('Location: ' . SITE_URL . '/admin/leads.php'); exit; }

$flashError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $flashError = 'Security check failed. Please try again.';
    } elseif (($_POST['action'] ?? '') === 'status') {
        $new = $_POST['status'] ?? '';
        if (in_array($new, lead_statuses(), true)) {
            $pdo->prepare("UPDATE leads SET status = ? WHERE id = ?")->execute([$new, $id]);
            header('Location: ' . SITE_URL . '/admin/lead-view.php?id=' . $id . '&ok=' . rawurlencode('Status updated to ' . $new));
            exit;
        }
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $pdo->prepare("DELETE FROM leads WHERE id = ?")->execute([$id]);
        header('Location: ' . SITE_URL . '/admin/leads.php?ok=' . rawurlencode('Lead deleted'));
        exit;
    }
}

$adminTitle = 'Lead #' . $id;
$adminAction = '<a href="' . SITE_URL . '/admin/leads.php" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left"></i> Back to leads</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';
$statuses = lead_statuses();
?>

<div class="grid" style="grid-template-columns:1.6fr 1fr;gap:20px;align-items:start">

  <div class="admin-card">
    <h2 style="font-size:1.15rem;margin-bottom:16px"><?= e($lead['name'] ?: 'Unnamed lead') ?></h2>
    <table style="width:100%;font-size:14px">
      <tr><td style="color:var(--text-faint);padding:8px 0;width:140px">WhatsApp</td><td><a href="https://wa.me/<?= e(preg_replace('/\D/', '', $lead['whatsapp'])) ?>" target="_blank" rel="noopener" style="color:var(--violet-dk);font-weight:700"><?= e($lead['whatsapp']) ?> <i class="bi bi-whatsapp"></i></a></td></tr>
      <tr><td style="color:var(--text-faint);padding:8px 0">Business</td><td><?= e($lead['business_name'] ?: '—') ?></td></tr>
      <tr><td style="color:var(--text-faint);padding:8px 0">Service</td><td><?= e($lead['service_type'] ?: '—') ?></td></tr>
      <tr><td style="color:var(--text-faint);padding:8px 0">Budget</td><td><?= e($lead['budget'] ?: '—') ?></td></tr>
      <tr><td style="color:var(--text-faint);padding:8px 0">Source</td><td><span class="badge"><?= e($lead['source']) ?></span></td></tr>
      <tr><td style="color:var(--text-faint);padding:8px 0">Received</td><td><?= date('d M Y, H:i', strtotime($lead['created_at'])) ?></td></tr>
    </table>
    <?php if (trim($lead['message'])): ?>
      <div style="margin-top:14px;padding:14px;background:var(--bg-soft);border-radius:10px;font-size:14px;white-space:pre-wrap"><?= e($lead['message']) ?></div>
    <?php endif; ?>

    <form method="post" style="margin-top:18px" onsubmit="return confirm('Delete this lead permanently?')">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="delete">
      <button class="btn btn-ghost btn-sm" style="color:var(--red);border-color:#FECDCA"><i class="bi bi-trash"></i> Delete lead</button>
    </form>
  </div>

  <div class="admin-card">
    <h3 style="font-size:1rem;margin-bottom:6px">Order workflow</h3>
    <p style="color:var(--text-faint);font-size:13px;margin-bottom:14px">Move this lead through the pipeline.</p>

    <div style="display:flex;flex-direction:column;gap:8px;margin-bottom:18px">
      <?php foreach ($statuses as $i => $s):
        $curIdx = array_search($lead['status'], $statuses, true);
        $done = $i <= $curIdx; ?>
        <div style="display:flex;align-items:center;gap:10px;font-size:13.5px;<?= $done ? '' : 'opacity:.45' ?>">
          <span style="width:22px;height:22px;border-radius:50%;display:grid;place-items:center;font-size:12px;<?= $done ? 'background:var(--violet);color:#fff' : 'background:var(--bg-tint);color:var(--text-faint)' ?>">
            <i class="bi <?= $done ? 'bi-check' : 'bi-circle' ?>"></i>
          </span>
          <strong><?= e($s) ?></strong>
        </div>
      <?php endforeach; ?>
    </div>

    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="status">
      <label style="font-weight:600;font-size:13.5px;display:block;margin-bottom:6px">Set status</label>
      <select name="status" class="form-control" style="margin-bottom:12px">
        <?php foreach ($statuses as $s): ?>
          <option value="<?= e($s) ?>" <?= $lead['status']===$s ? 'selected' : '' ?>><?= e($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary btn-block btn-sm"><i class="bi bi-check2"></i> Update status</button>
    </form>
  </div>

</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
