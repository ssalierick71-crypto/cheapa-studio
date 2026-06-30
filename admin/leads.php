<?php
$adminTitle = 'Leads & Orders';
require_once dirname(__DIR__) . '/includes/admin-header.php';

$filter = trim($_GET['status'] ?? '');
$statuses = lead_statuses();

if ($filter && in_array($filter, $statuses, true)) {
    $stmt = $pdo->prepare("SELECT * FROM leads WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
    $leads = $stmt->fetchAll();
} else {
    $leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC")->fetchAll();
}
?>

<div class="stage-selector" style="justify-content:flex-start;margin-bottom:20px">
  <a href="<?= SITE_URL ?>/admin/leads.php" class="stage-chip <?= $filter==='' ? 'active' : '' ?>" style="text-decoration:none">All</a>
  <?php foreach ($statuses as $s): ?>
    <a href="?status=<?= rawurlencode($s) ?>" class="stage-chip <?= $filter===$s ? 'active' : '' ?>" style="text-decoration:none"><?= e($s) ?></a>
  <?php endforeach; ?>
</div>

<div class="admin-card">
  <?php if (!$leads): ?>
    <p style="color:var(--text-faint)">No leads<?= $filter ? ' with status “'.e($filter).'”' : '' ?> yet.</p>
  <?php else: ?>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>When</th><th>Name</th><th>WhatsApp</th><th>Business</th><th>Service</th><th>Source</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($leads as $l): ?>
        <tr>
          <td style="white-space:nowrap"><?= date('d M Y, H:i', strtotime($l['created_at'])) ?></td>
          <td><?= e($l['name'] ?: '—') ?></td>
          <td><?= e($l['whatsapp']) ?></td>
          <td><?= e($l['business_name'] ?: '—') ?></td>
          <td><?= e($l['service_type'] ?: '—') ?></td>
          <td><span class="badge"><?= e($l['source']) ?></span></td>
          <td><span class="badge" style="<?= status_style($l['status']) ?>"><?= e($l['status']) ?></span></td>
          <td>
            <div class="table-actions">
              <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $l['whatsapp'])) ?>" target="_blank" rel="noopener" class="btn-icon" title="WhatsApp" style="color:var(--wa)"><i class="bi bi-whatsapp"></i></a>
              <a href="<?= SITE_URL ?>/admin/lead-view.php?id=<?= (int)$l['id'] ?>" class="btn-icon" title="View"><i class="bi bi-eye"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
