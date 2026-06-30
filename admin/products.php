<?php
require_once dirname(__DIR__) . '/config.php';
$adminTitle  = 'Shop Products';
$adminAction = '<a href="' . SITE_URL . '/admin/product-edit.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Add Product</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';

$products = $pdo->query("SELECT * FROM products ORDER BY sort_order ASC, id ASC")->fetchAll();
?>

<div class="admin-card">
  <?php if (!$products): ?>
    <p style="color:var(--text-faint)">No products yet. <a href="<?= SITE_URL ?>/admin/product-edit.php" style="color:var(--violet-dk)">Add your first product</a>.</p>
  <?php else: ?>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th></th><th>Name</th><th>Category</th><th>Price</th><th>Active</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td><?php if ($im = img_url('products', $p['image'])): ?><img src="<?= e($im) ?>" class="admin-thumb" alt=""><?php else: ?><span class="admin-thumb" style="display:grid;place-items:center;color:var(--violet)"><i class="bi bi-bag"></i></span><?php endif; ?></td>
          <td><strong><?= e($p['name']) ?></strong></td>
          <td><span class="badge"><?= e($p['category']) ?></span></td>
          <td>
            <?php if (is_configurable($p)): ?>
              <?= ugx($p['price_ugx']) ?> <span style="color:var(--text-faint);font-size:12px">/ <?= e(rtrim($p['unit_label'],'s')) ?> · MOQ <?= (int)$p['moq'] ?></span>
            <?php else: ?>
              <?= ugx($p['price_ugx']) ?>
            <?php endif; ?>
          </td>
          <td><?= $p['is_active'] ? '<span class="badge" style="background:#D1FADF;color:#027A48">Live</span>' : '<span class="badge">Hidden</span>' ?></td>
          <td>
            <div class="table-actions">
              <a href="<?= SITE_URL ?>/admin/product-edit.php?id=<?= (int)$p['id'] ?>" class="btn-icon" title="Edit"><i class="bi bi-pencil"></i></a>
              <form method="post" action="<?= SITE_URL ?>/admin/product-delete.php" onsubmit="return confirm('Delete this product?')" style="display:inline">
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
