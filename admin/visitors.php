<?php
require_once dirname(__DIR__) . '/config.php';
$adminTitle = 'Visitors';
require_once dirname(__DIR__) . '/includes/admin-header.php';

$days = (int)($_GET['days'] ?? 14);
if (!in_array($days, [7, 14, 30], true)) $days = 14;

$q = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$todayPV = $q("SELECT COUNT(*) FROM visits WHERE is_bot=0 AND day=" . sql_today());
$todayUV = $q("SELECT COUNT(DISTINCT ip_hash) FROM visits WHERE is_bot=0 AND day=" . sql_today());
$weekPV  = $q("SELECT COUNT(*) FROM visits WHERE is_bot=0 AND day >= " . sql_days_ago(6));
$weekUV  = $q("SELECT COUNT(DISTINCT ip_hash) FROM visits WHERE is_bot=0 AND day >= " . sql_days_ago(6));
$totalPV = $q("SELECT COUNT(*) FROM visits WHERE is_bot=0");

// per-day series
$rows = [];
$st = $pdo->query("SELECT day, COUNT(*) pv, COUNT(DISTINCT ip_hash) uv FROM visits WHERE is_bot=0 AND day >= " . sql_days_ago($days - 1) . " GROUP BY day");
foreach ($st as $r) $rows[$r['day']] = $r;
$series = [];
for ($i = $days - 1; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $series[] = ['day' => $d, 'pv' => (int)($rows[$d]['pv'] ?? 0), 'uv' => (int)($rows[$d]['uv'] ?? 0)];
}
$maxUV = max(1, max(array_column($series, 'uv')));

$topPages = $pdo->query("SELECT path, COUNT(*) c FROM visits WHERE is_bot=0 GROUP BY path ORDER BY c DESC LIMIT 6")->fetchAll();
$devices  = $pdo->query("SELECT device, COUNT(*) c FROM visits WHERE is_bot=0 GROUP BY device")->fetchAll();
$devTotal = array_sum(array_column($devices, 'c')) ?: 1;
$refs     = $pdo->query("SELECT referrer, COUNT(*) c FROM visits WHERE is_bot=0 AND referrer<>'' GROUP BY referrer ORDER BY c DESC LIMIT 6")->fetchAll();
$recent   = $pdo->query("SELECT created_at, path, device, referrer FROM visits WHERE is_bot=0 ORDER BY id DESC LIMIT 12")->fetchAll();

$pageLabel = fn($p) => $p === 'index.php' ? 'Home' : ucfirst(str_replace(['.php', '-'], ['', ' '], $p));
?>

<div class="admin-topbar" style="margin-top:-8px">
  <div></div>
  <div class="stage-selector" style="margin:0">
    <?php foreach ([7,14,30] as $d): ?>
      <a href="?days=<?= $d ?>" class="stage-chip <?= $days===$d?'active':'' ?>" style="text-decoration:none"><?= $d ?> days</a>
    <?php endforeach; ?>
  </div>
</div>

<div class="stat-grid">
  <div class="stat-card"><div class="sc-ico" style="background:#FEF0C7;color:#B54708"><i class="bi bi-person-check"></i></div><b><?= $todayUV ?></b><span>Visitors today</span></div>
  <div class="stat-card"><div class="sc-ico"><i class="bi bi-eye"></i></div><b><?= $todayPV ?></b><span>Page views today</span></div>
  <div class="stat-card"><div class="sc-ico" style="background:#E0E7FF;color:#3538CD"><i class="bi bi-people"></i></div><b><?= $weekUV ?></b><span>Visitors (7 days)</span></div>
  <div class="stat-card"><div class="sc-ico" style="background:#D1FADF;color:#027A48"><i class="bi bi-bar-chart"></i></div><b><?= number_format($totalPV) ?></b><span>Total page views</span></div>
</div>

<div class="admin-card" style="margin-bottom:20px">
  <h2 style="font-size:1.1rem;margin-bottom:4px">Visitors per day</h2>
  <p style="color:var(--text-faint);font-size:12.5px;margin-bottom:16px">Unique visitors each day (bots excluded). Last <?= $days ?> days.</p>
  <div class="vchart">
    <?php foreach ($series as $s): $hpct = round($s['uv'] / $maxUV * 100); ?>
    <div class="vbar-col" title="<?= date('D d M', strtotime($s['day'])) ?>: <?= $s['uv'] ?> visitors, <?= $s['pv'] ?> views">
      <div class="vbar-val"><?= $s['uv'] ?: '' ?></div>
      <div class="vbar" style="height:<?= max(2,$hpct) ?>%"></div>
      <div class="vbar-day"><?= date('j', strtotime($s['day'])) ?><?= ($s['day']===date('Y-m-d')) ? '<span class="vbar-today"></span>' : '' ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;margin-bottom:20px">
  <div class="admin-card">
    <h3 style="font-size:1rem;margin-bottom:14px">Top pages</h3>
    <?php if (!$topPages): ?><p style="color:var(--text-faint)">No data yet.</p><?php endif; ?>
    <?php $tpMax = $topPages ? (int)$topPages[0]['c'] : 1; foreach ($topPages as $p): ?>
      <div class="vrow"><span><?= e($pageLabel($p['path'])) ?></span><span class="vrow-bar"><span style="width:<?= round($p['c']/$tpMax*100) ?>%"></span></span><b><?= $p['c'] ?></b></div>
    <?php endforeach; ?>
  </div>

  <div class="admin-card">
    <h3 style="font-size:1rem;margin-bottom:14px">Devices</h3>
    <?php foreach ($devices as $d): $pct = round($d['c']/$devTotal*100); ?>
      <div class="vrow"><span><i class="bi <?= $d['device']==='mobile'?'bi-phone':'bi-laptop' ?>"></i> <?= ucfirst($d['device']) ?></span><span class="vrow-bar"><span style="width:<?= $pct ?>%"></span></span><b><?= $pct ?>%</b></div>
    <?php endforeach; ?>
    <?php if (!$devices): ?><p style="color:var(--text-faint)">No data yet.</p><?php endif; ?>
    <h3 style="font-size:1rem;margin:18px 0 14px">Where they came from</h3>
    <?php if (!$refs): ?><p style="color:var(--text-faint);font-size:13px">Mostly direct visits so far.</p><?php endif; ?>
    <?php foreach ($refs as $r): ?>
      <div class="vrow"><span><?= e($r['referrer']) ?></span><b><?= $r['c'] ?></b></div>
    <?php endforeach; ?>
  </div>
</div>

<div class="admin-card">
  <h3 style="font-size:1rem;margin-bottom:14px">Recent visits</h3>
  <?php if (!$recent): ?><p style="color:var(--text-faint)">No visits recorded yet. Open the public site in another browser to see it here.</p><?php else: ?>
  <div style="overflow-x:auto">
    <table class="data-table">
      <thead><tr><th>When</th><th>Page</th><th>Device</th><th>Source</th></tr></thead>
      <tbody>
        <?php foreach ($recent as $v): ?>
        <tr>
          <td style="white-space:nowrap"><?= date('d M, H:i', strtotime($v['created_at'])) ?></td>
          <td><?= e($pageLabel($v['path'])) ?></td>
          <td><span class="badge"><i class="bi <?= $v['device']==='mobile'?'bi-phone':'bi-laptop' ?>"></i> <?= ucfirst($v['device']) ?></span></td>
          <td><?= $v['referrer'] ? e($v['referrer']) : '<span style="color:var(--text-faint)">Direct</span>' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
  <p style="color:var(--text-faint);font-size:12px;margin-top:14px"><i class="bi bi-shield-lock"></i> Privacy-friendly: visitor IP addresses are hashed, never stored. Bots and link-preview bots are excluded from these numbers.</p>
</div>

<?php require_once dirname(__DIR__) . '/includes/admin-footer.php'; ?>
