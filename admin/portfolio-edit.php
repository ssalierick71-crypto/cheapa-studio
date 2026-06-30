<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$case = ['title'=>'','industry'=>'','problem'=>'','solution'=>'','result'=>'','before_image'=>'','after_image'=>'','is_active'=>1,'sort_order'=>0];
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { header('Location: ' . SITE_URL . '/admin/portfolio.php'); exit; }
    $case = $found;
}

$flashError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $flashError = 'Security check failed. Please try again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        if ($title === '') {
            $flashError = 'Title is required.';
        } else {
            $before = upload_image('before_image', 'portfolio', $case['before_image'] ?: null);
            $after  = upload_image('after_image',  'portfolio', $case['after_image']  ?: null);
            $data = [
                $title, trim($_POST['industry'] ?? 'General') ?: 'General',
                trim($_POST['problem'] ?? ''), trim($_POST['solution'] ?? ''), trim($_POST['result'] ?? ''),
                $before ?? '', $after ?? '',
                isset($_POST['is_active']) ? 1 : 0, (int)($_POST['sort_order'] ?? 0),
            ];
            if ($id) {
                $data[] = $id;
                $pdo->prepare("UPDATE portfolio SET title=?,industry=?,problem=?,solution=?,result=?,before_image=?,after_image=?,is_active=?,sort_order=? WHERE id=?")->execute($data);
            } else {
                $pdo->prepare("INSERT INTO portfolio (title,industry,problem,solution,result,before_image,after_image,is_active,sort_order) VALUES (?,?,?,?,?,?,?,?,?)")->execute($data);
            }
            header('Location: ' . SITE_URL . '/admin/portfolio.php?ok=' . rawurlencode('Case study saved'));
            exit;
        }
        $case = array_merge($case, $_POST);
    }
}

$adminTitle  = $id ? 'Edit Case Study' : 'Add Case Study';
$adminAction = '<a href="' . SITE_URL . '/admin/portfolio.php" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-left"></i> Back</a>';
require_once dirname(__DIR__) . '/includes/admin-header.php';
?>

<div class="admin-card" style="max-width:760px">
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-row">
      <div class="form-group"><label>Title <span class="req">*</span></label><input name="title" class="form-control" required value="<?= e($case['title']) ?>"></div>
      <div class="form-group"><label>Industry</label><input name="industry" class="form-control" value="<?= e($case['industry']) ?>" placeholder="Salon, Shop, Restaurant…"></div>
    </div>
    <div class="form-group"><label>Problem</label><textarea name="problem" class="form-control"><?= e($case['problem']) ?></textarea></div>
    <div class="form-group"><label>Solution</label><textarea name="solution" class="form-control"><?= e($case['solution']) ?></textarea></div>
    <div class="form-group"><label>Result</label><textarea name="result" class="form-control"><?= e($case['result']) ?></textarea></div>

    <div class="form-row">
      <div class="form-group">
        <label>Before image</label>
        <?php if ($bi = img_url('portfolio', $case['before_image'])): ?><img src="<?= e($bi) ?>" class="admin-thumb" style="width:120px;height:80px;filter:grayscale(1);margin-bottom:8px" alt=""><?php endif; ?>
        <input type="file" name="before_image" accept="image/*" class="form-control">
      </div>
      <div class="form-group">
        <label>After image</label>
        <?php if ($ai = img_url('portfolio', $case['after_image'])): ?><img src="<?= e($ai) ?>" class="admin-thumb" style="width:120px;height:80px;margin-bottom:8px" alt=""><?php endif; ?>
        <input type="file" name="after_image" accept="image/*" class="form-control">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group"><label>Sort order</label><input name="sort_order" type="number" class="form-control" value="<?= e($case['sort_order']) ?>"></div>
      <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:12px">
        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="is_active" <?= $case['is_active']?'checked':'' ?>> Active (visible)</label>
      </div>
    </div>
    <button class="btn btn-primary btn-lg"><i class="bi bi-check2"></i> Save Case Study</button>
  </form>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
