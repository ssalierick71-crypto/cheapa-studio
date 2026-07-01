<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$cats = ['Branding','Print','Digital','Web'];
$id = (int)($_GET['id'] ?? 0);
$product = ['name'=>'','slug'=>'','category'=>'Branding','unit_type'=>'fixed','unit_label'=>'pieces','price_ugx'=>'','moq'=>1,'step'=>1,'description'=>'','variants'=>'','design_available'=>0,'design_fee'=>10000,'image'=>'','is_active'=>1,'sort_order'=>0];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { header('Location: ' . SITE_URL . '/admin/products.php'); exit; }
    $product = $found;
}

$flashError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $flashError = 'Security check failed. Please try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $flashError = 'Product name is required.';
        } else {
            $slug  = slugify($_POST['slug'] ?? '' ?: $name);
            $cat   = in_array($_POST['category'] ?? '', $cats, true) ? $_POST['category'] : 'Branding';
            $unitType = in_array($_POST['unit_type'] ?? '', ['fixed','piece','meter'], true) ? $_POST['unit_type'] : 'fixed';
            $unitLabel = $unitType === 'meter' ? 'meters' : ($unitType === 'piece' ? 'pieces' : '');
            $image = upload_image('image', 'products', $product['image'] ?: null);
            $data = [
                $name, $slug, $cat, $unitType, $unitLabel, (int)($_POST['price_ugx'] ?? 0),
                max(1, (int)($_POST['moq'] ?? 1)), max(1, (int)($_POST['step'] ?? 1)),
                trim($_POST['description'] ?? ''), trim($_POST['variants'] ?? ''),
                isset($_POST['design_available']) ? 1 : 0, (int)($_POST['design_fee'] ?? 0),
                $image ?? '', isset($_POST['is_active']) ? 1 : 0, (int)($_POST['sort_order'] ?? 0),
            ];
            try {
                if ($id) {
                    $data[] = $id;
                    $pdo->prepare("UPDATE products SET name=?,slug=?,category=?,unit_type=?,unit_label=?,price_ugx=?,moq=?,step=?,description=?,variants=?,design_available=?,design_fee=?,image=?,is_active=?,sort_order=? WHERE id=?")->execute($data);
                } else {
                    $pdo->prepare("INSERT INTO products (name,slug,category,unit_type,unit_label,price_ugx,moq,step,description,variants,design_available,design_fee,image,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute($data);
                }
                header('Location: ' . SITE_URL . '/admin/products.php?ok=' . rawurlencode('Product saved'));
                exit;
            } catch (PDOException $ex) {
                $msg = $ex->getMessage();
                error_log('Product save failed: ' . $msg);
                $flashError = (stripos($msg, 'duplicate') !== false || stripos($msg, 'unique') !== false) ? 'That slug is already used. Choose another.' : 'Could not save. Check your inputs.';
            }
        }
        $product = array_merge($product, $_POST);
    }
}

$adminTitle  = $id ? 'Edit Product' : 'Add Product';
$adminAction = '<a href="' . SITE_URL . '/admin/products.php" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left"></i> Back</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';
?>

<div class="admin-card" style="max-width:700px">
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-row">
      <div class="form-group"><label>Product name <span class="req">*</span></label><input name="name" class="form-control" required value="<?= e($product['name']) ?>"></div>
      <div class="form-group"><label>Slug (URL id)</label><input name="slug" class="form-control" value="<?= e($product['slug']) ?>" placeholder="auto from name"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Category</label>
        <select name="category" class="form-control">
          <?php foreach ($cats as $c): ?><option <?= $product['category']===$c?'selected':'' ?>><?= $c ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="form-group"><label>Pricing type</label>
        <select name="unit_type" class="form-control">
          <option value="fixed"  <?= $product['unit_type']==='fixed'?'selected':'' ?>>Fixed price (one-off)</option>
          <option value="piece"  <?= $product['unit_type']==='piece'?'selected':'' ?>>Per piece (with MOQ)</option>
          <option value="meter"  <?= $product['unit_type']==='meter'?'selected':'' ?>>Per meter (with MOQ)</option>
        </select>
        <div class="form-hint">Fixed = logo/website. Per piece = cards/flyers. Per meter = banners.</div>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Price (UGX) <span style="font-weight:400;color:var(--text-faint)">— per unit, or flat for fixed</span></label><input name="price_ugx" type="number" class="form-control" value="<?= e($product['price_ugx']) ?>"></div>
      <div class="form-group" style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div><label>MOQ</label><input name="moq" type="number" class="form-control" value="<?= e($product['moq']) ?>"></div>
        <div><label>Step</label><input name="step" type="number" class="form-control" value="<?= e($product['step']) ?>"></div>
      </div>
    </div>
    <div class="form-group">
      <label>Variants <span style="font-weight:400;color:var(--text-faint)">— one per line as <code>Label=price</code> (optional)</span></label>
      <textarea name="variants" class="form-control" placeholder="Single sided=200&#10;Double sided=300"><?= e($product['variants']) ?></textarea>
      <div class="form-hint">Leave blank if there are no options. Variant price overrides the unit price.</div>
    </div>
    <div class="form-row">
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:12px">
        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="design_available" <?= $product['design_available']?'checked':'' ?>> Offer "design it for me"</label>
      </div>
      <div class="form-group"><label>Design fee (UGX)</label><input name="design_fee" type="number" class="form-control" value="<?= e($product['design_fee']) ?>"></div>
    </div>
    <div class="form-group"><label>Description</label><textarea name="description" class="form-control"><?= e($product['description']) ?></textarea></div>
    <div class="form-group">
      <label>Image</label>
      <?php if ($im = img_url('products', $product['image'])): ?><img src="<?= e($im) ?>" class="admin-thumb" style="width:120px;height:80px;margin-bottom:8px" alt=""><?php endif; ?>
      <input type="file" name="image" accept="image/*" class="form-control">
      <div class="form-hint">Leave empty to keep the current image.</div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Sort order</label><input name="sort_order" type="number" class="form-control" value="<?= e($product['sort_order']) ?>"></div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:12px">
        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="is_active" <?= $product['is_active']?'checked':'' ?>> Active (visible in shop)</label>
      </div>
    </div>
    <button class="btn btn-primary btn-lg"><i class="bi bi-check2"></i> Save Product</button>
  </form>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
