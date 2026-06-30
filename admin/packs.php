<?php
require_once dirname(__DIR__) . '/config.php';
$adminTitle  = 'Business Growth Packs';
$adminAction = '<a href="' . SITE_URL . '/admin/pack-edit.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Pack</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';

$packs = $pdo->query("SELECT * FROM packs ORDER BY sort_order ASC, id ASC")->fetchAll();
?>

<div class="admin-card">
  <?php if (!$packs): ?>
    <p style="color:var(--text-faint)">No packs yet. <a href="<?= SITE_URL ?>/admin/pack-edit.php" style="color:var(--violet-dk)">Add your first pack</a>.</p>
  <?php else: ?>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th></th><th>Name</th><th>Stage</th><th>Price</th><th>Featured</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($packs as $p): ?>
        <tr>
          <td><?php if ($im = img_url('packs', $p['image'])): ?><img src="<?= e($im) ?>" class="admin-thumb" alt=""><?php else: ?><span class="admin-thumb" style="display:grid;place-items:center;color:var(--violet)"><i class="bi bi-box-seam"></i></span><?php endif; ?></td>
          <td><strong><?= e($p['name']) ?></strong></td>
          <td><?= e($p['stage']) ?></td>
          <td><?= ugx($p['price_ugx']) ?></td>
          <td><?= $p['is_featured'] ? '<i class="bi bi-star-fill" style="color:var(--amber)"></i>' : '—' ?></td>
          <td><?= $p['is_active'] ? '<span class="badge" style="background:#D1FADF;color:#027A48">Live</span>' : '<span class="badge">Hidden</span>' ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= SITE_URL ?>/admin/pack-items.php?pack_id=<?= (int)$p['id'] ?>" class="btn-icon" title="Manage what's inside"><i class="bi bi-box2-heart"></i></a>
              <a href="<?= SITE_URL ?>/admin/pack-edit.php?id=<?= (int)$p['id'] ?>" class="btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
              <form method="post" action="<?= SITE_URL ?>/admin/pack-delete.php" onsubmit="return confirm('Delete this pack?')" style="display:inline">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn-icon danger" title="Delete"><i class="bi bi-trash"></i></button>
              </form>
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
