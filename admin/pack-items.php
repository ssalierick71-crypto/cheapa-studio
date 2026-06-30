<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$packId = (int)($_GET['pack_id'] ?? $_POST['pack_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM packs WHERE id = ?");
$stmt->execute([$packId]);
$pack = $stmt->fetch();
if (!$pack) { header('Location: ' . SITE_URL . '/admin/packs.php'); exit; }

$flashError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $flashError = 'Security check failed.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'add') {
            $label = trim($_POST['label'] ?? '');
            if ($label !== '') {
                $img = upload_image('image', 'pack-items', null);
                $maxSort = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM pack_items WHERE pack_id=" . $packId)->fetchColumn();
                $pdo->prepare("INSERT INTO pack_items (pack_id,label,blurb,image,sort_order) VALUES (?,?,?,?,?)")
                    ->execute([$packId, $label, trim($_POST['blurb'] ?? ''), $img ?? '', $maxSort + 1]);
            }
        } elseif ($action === 'update') {
            $iid = (int)($_POST['item_id'] ?? 0);
            $cur = $pdo->prepare("SELECT * FROM pack_items WHERE id=? AND pack_id=?");
            $cur->execute([$iid, $packId]);
            if ($row = $cur->fetch()) {
                $img = upload_image('image', 'pack-items', $row['image'] ?: null);
                $pdo->prepare("UPDATE pack_items SET label=?,blurb=?,image=?,sort_order=? WHERE id=?")
                    ->execute([trim($_POST['label'] ?? ''), trim($_POST['blurb'] ?? ''), $img ?? '', (int)($_POST['sort_order'] ?? 0), $iid]);
            }
        } elseif ($action === 'delete') {
            $iid = (int)($_POST['item_id'] ?? 0);
            $cur = $pdo->prepare("SELECT image FROM pack_items WHERE id=? AND pack_id=?");
            $cur->execute([$iid, $packId]);
            if ($row = $cur->fetch()) { delete_image('pack-items', $row['image']); $pdo->prepare("DELETE FROM pack_items WHERE id=?")->execute([$iid]); }
        }
        header('Location: ' . SITE_URL . '/admin/pack-items.php?pack_id=' . $packId . '&ok=' . rawurlencode('Saved'));
        exit;
    }
}

$items = $pdo->prepare("SELECT * FROM pack_items WHERE pack_id=? ORDER BY sort_order, id");
$items->execute([$packId]);
$items = $items->fetchAll();

$adminTitle  = "What's inside: " . $pack['name'];
$adminAction = '<a href="' . SITE_URL . '/admin/packs.php" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left"></i> Back to packs</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';
?>

<p style="color:var(--text-faint);font-size:13.5px;margin:-12px 0 18px">These items and images show on the public pack page (<a href="<?= SITE_URL ?>/pack.php?slug=<?= e($pack['slug']) ?>" target="_blank" style="color:var(--violet-dk)">view it</a>). Upload a photo of each item so buyers see exactly what's included.</p>

<!-- Add new item -->
<div class="admin-card" style="margin-bottom:20px">
  <h3 style="font-size:1.05rem;margin-bottom:14px"><i class="bi bi-plus-circle"></i> Add an item</h3>
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?><input type="hidden" name="action" value="add"><input type="hidden" name="pack_id" value="<?= $packId ?>">
    <div class="form-row">
      <div class="form-group"><label>Item name <span class="req">*</span></label><input name="label" class="form-control" required placeholder="e.g. 200 Business cards"></div>
      <div class="form-group"><label>Image</label><input type="file" name="image" accept="image/*" class="form-control"></div>
    </div>
    <div class="form-group"><label>Short description</label><input name="blurb" class="form-control" placeholder="One line about this item"></div>
    <button class="btn btn-primary"><i class="bi bi-plus-lg"></i> Add Item</button>
  </form>
</div>

<!-- Existing items -->
<div class="pack-items-grid">
  <?php if (!$items): ?>
    <p style="color:var(--text-faint)">No items yet. Add the first one above.</p>
  <?php endif; ?>
  <?php foreach ($items as $it): ?>
  <div class="admin-card pi-card">
    <form method="post" enctype="multipart/form-data">
      <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="pack_id" value="<?= $packId ?>"><input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
      <div class="pi-media">
        <?php if ($im = img_url('pack-items', $it['image'])): ?><img src="<?= e($im) ?>" alt=""><?php else: ?><span class="pi-noimg"><i class="bi bi-image"></i></span><?php endif; ?>
      </div>
      <div class="form-group" style="margin:12px 0 8px"><label>Item name</label><input name="label" class="form-control" value="<?= e($it['label']) ?>"></div>
      <div class="form-group" style="margin-bottom:8px"><label>Description</label><input name="blurb" class="form-control" value="<?= e($it['blurb']) ?>"></div>
      <div class="form-row" style="gap:10px">
        <div class="form-group" style="margin-bottom:8px"><label>Replace image</label><input type="file" name="image" accept="image/*" class="form-control"></div>
        <div class="form-group" style="margin-bottom:8px;max-width:90px"><label>Order</label><input name="sort_order" type="number" class="form-control" value="<?= (int)$it['sort_order'] ?>"></div>
      </div>
      <div style="display:flex;gap:8px">
        <button class="btn btn-primary btn-sm" style="flex:1"><i class="bi bi-check2"></i> Save</button>
      </div>
    </form>
    <form method="post" onsubmit="return confirm('Delete this item?')" style="margin-top:8px">
      <?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="pack_id" value="<?= $packId ?>"><input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
      <button class="btn btn-ghost btn-sm btn-block" style="color:var(--red);border-color:#FECDCA"><i class="bi bi-trash"></i> Delete</button>
    </form>
  </div>
  <?php endforeach; ?>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
