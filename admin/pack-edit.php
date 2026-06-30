<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$pack = ['name'=>'','slug'=>'','stage'=>'Starting','price_ugx'=>'','tagline'=>'','best_for'=>'','features'=>'','image'=>'','is_featured'=>0,'is_active'=>1,'sort_order'=>0];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM packs WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { header('Location: ' . SITE_URL . '/admin/packs.php'); exit; }
    $pack = $found;
}

$flashError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $flashError = 'Security check failed. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $flashError = 'Pack name is required.';
        } else {
            $slug   = slugify($_POST['slug'] ?? '' ?: $name);
            $stage  = in_array($_POST['stage'] ?? '', ['Starting','Growing','Established','Authority'], true) ? $_POST['stage'] : 'Starting';
            $price  = (int)($_POST['price_ugx'] ?? 0);
            $image  = upload_image('image', 'packs', $pack['image'] ?: null);
            $data = [
                $name, $slug, $stage, $price,
                trim($_POST['tagline'] ?? ''), trim($_POST['best_for'] ?? ''),
                trim($_POST['features'] ?? ''), $image ?? '',
                isset($_POST['is_featured']) ? 1 : 0,
                isset($_POST['is_active']) ? 1 : 0,
                (int)($_POST['sort_order'] ?? 0),
            ];
            try {
                if ($id) {
                    $data[] = $id;
                    $pdo->prepare("UPDATE packs SET name=?,slug=?,stage=?,price_ugx=?,tagline=?,best_for=?,features=?,image=?,is_featured=?,is_active=?,sort_order=? WHERE id=?")->execute($data);
                } else {
                    $pdo->prepare("INSERT INTO packs (name,slug,stage,price_ugx,tagline,best_for,features,image,is_featured,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?)")->execute($data);
                }
                header('Location: ' . SITE_URL . '/admin/packs.php?ok=' . rawurlencode('Pack saved'));
                exit;
            } catch (PDOException $ex) {
                $flashError = (strpos($ex->getMessage(), 'Duplicate') !== false) ? 'That slug is already used. Choose another.' : 'Could not save. Check your inputs.';
            }
        }
        $pack = array_merge($pack, $_POST);
    }
}

$adminTitle  = $id ? 'Edit Pack' : 'Add Pack';
$adminAction = '<a href="' . SITE_URL . '/admin/packs.php" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left"></i> Back</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';
?>

<div class="admin-card" style="max-width:760px">
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-row">
      <div class="form-group"><label>Pack name <span class="req">*</span></label><input name="name" class="form-control" required value="<?= e($pack['name']) ?>"></div>
      <div class="form-group"><label>Slug (URL id)</label><input name="slug" class="form-control" value="<?= e($pack['slug']) ?>" placeholder="auto from name"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Stage</label>
        <select name="stage" class="form-control">
          <?php foreach (['Starting','Growing','Established','Authority'] as $s): ?>
            <option <?= $pack['stage']===$s?'selected':'' ?>><?= $s ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Price (UGX)</label><input name="price_ugx" type="number" class="form-control" value="<?= e($pack['price_ugx']) ?>"></div>
    </div>
    <div class="form-group"><label>Tagline</label><input name="tagline" class="form-control" value="<?= e($pack['tagline']) ?>"></div>
    <div class="form-group"><label>Best for</label><input name="best_for" class="form-control" value="<?= e($pack['best_for']) ?>"></div>
    <div class="form-group"><label>Features <span style="font-weight:400;color:var(--text-faint)">(one per line)</span></label><textarea name="features" class="form-control" style="min-height:150px"><?= e($pack['features']) ?></textarea></div>

    <div class="form-group">
      <label>Image</label>
      <?php if ($im = img_url('packs', $pack['image'])): ?><img src="<?= e($im) ?>" class="admin-thumb" style="width:120px;height:80px;margin-bottom:8px" alt=""><?php endif; ?>
      <input type="file" name="image" accept="image/*" class="form-control">
      <div class="form-hint">Leave empty to keep the current image. JPG/PNG/WebP.</div>
    </div>

    <div class="form-row">
      <div class="form-group"><label>Sort order</label><input name="sort_order" type="number" class="form-control" value="<?= e($pack['sort_order']) ?>"></div>
      <div class="form-group" style="display:flex;gap:18px;align-items:flex-end;padding-bottom:12px">
        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="is_featured" <?= $pack['is_featured']?'checked':'' ?>> Featured</label>
        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="is_active" <?= $pack['is_active']?'checked':'' ?>> Active</label>
      </div>
    </div>

    <button class="btn btn-primary btn-lg"><i class="bi bi-check2"></i> Save Pack</button>
    <?php if ($id): ?><a href="<?= SITE_URL ?>/admin/pack-items.php?pack_id=<?= $id ?>" class="btn btn-ghost btn-lg" style="margin-left:8px"><i class="bi bi-box2-heart"></i> Manage what's inside</a><?php endif; ?>
  </form>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
