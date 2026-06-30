<?php
require_once dirname(__DIR__) . '/config.php';
$adminTitle  = 'Portfolio';
$adminAction = '<a href="' . SITE_URL . '/admin/portfolio-edit.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Case Study</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';

$cases = $pdo->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id ASC")->fetchAll();
?>

<div class="admin-card">
  <?php if (!$cases): ?>
    <p style="color:var(--text-faint)">No case studies yet. <a href="<?= SITE_URL ?>/admin/portfolio-edit.php" style="color:var(--violet-dk)">Add your first</a>.</p>
  <?php else: ?>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>Before</th><th>After</th><th>Title</th><th>Industry</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($cases as $c): ?>
        <tr>
          <td><?php if ($bi = img_url('portfolio', $c['before_image'])): ?><img src="<?= e($bi) ?>" class="admin-thumb" style="filter:grayscale(1)" alt=""><?php else: ?>—<?php endif; ?></td>
          <td><?php if ($ai = img_url('portfolio', $c['after_image'])): ?><img src="<?= e($ai) ?>" class="admin-thumb" alt=""><?php else: ?>—<?php endif; ?></td>
          <td><strong><?= e($c['title']) ?></strong></td>
          <td><span class="badge"><?= e($c['industry']) ?></span></td>
          <td><?= $c['is_active'] ? '<span class="badge" style="background:#D1FADF;color:#027A48">Live</span>' : '<span class="badge">Hidden</span>' ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= SITE_URL ?>/admin/portfolio-edit.php?id=<?= (int)$c['id'] ?>" class="btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
              <form method="post" action="<?= SITE_URL ?>/admin/portfolio-delete.php" onsubmit="return confirm('Delete this case study?')" style="display:inline">
                <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
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
