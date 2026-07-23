<?php
// Admin dashboard — traffic statistics, time on page + recent enquiries
require_once __DIR__ . '/lib-admin.php';
require_login();

$pdo = db();

/* ---------- period switcher: days / weeks / months ---------- */
$RANGES = ['day' => 'Po dňoch', 'week' => 'Po týždňoch', 'month' => 'Po mesiacoch'];
$range = $_GET['range'] ?? 'day';
if (!isset($RANGES[$range])) $range = 'day';

// bucket list (oldest -> newest) and the label shown under the chart
$buckets = [];   // key => ['label' => ..., 'views' => 0, 'dur_sum' => 0, 'dur_n' => 0]
if ($range === 'day') {
    for ($i = 29; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $buckets[$d] = ['label' => date('j. n.', strtotime($d))];
    }
    $since = array_key_first($buckets);
} elseif ($range === 'week') {
    for ($i = 11; $i >= 0; $i--) {
        $mon = date('Y-m-d', strtotime("monday -$i weeks"));
        $buckets[date('o-W', strtotime($mon))] = ['label' => date('j. n.', strtotime($mon))];
    }
    $since = date('Y-m-d', strtotime('monday -11 weeks'));
} else {
    for ($i = 11; $i >= 0; $i--) {
        $m = date('Y-m-01', strtotime("first day of -$i months"));
        $buckets[substr($m, 0, 7)] = ['label' => date('n/y', strtotime($m))];
    }
    $since = date('Y-m-01', strtotime('first day of -11 months'));
}
foreach ($buckets as $k => $v) {
    $buckets[$k] += ['views' => 0, 'dur_sum' => 0, 'dur_n' => 0];
}

$bucket_of = function (string $day) use ($range): string {
    if ($range === 'day') return $day;
    if ($range === 'week') return date('o-W', strtotime($day));
    return substr($day, 0, 7);
};

$st = $pdo->prepare('SELECT day, COUNT(*) AS views,
                            SUM(COALESCE(duration, 0)) AS dur_sum,
                            SUM(CASE WHEN duration IS NOT NULL THEN 1 ELSE 0 END) AS dur_n
                     FROM views WHERE day >= ? GROUP BY day');
$st->execute([$since]);
foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $k = $bucket_of($r['day']);
    if (!isset($buckets[$k])) continue;
    $buckets[$k]['views']   += (int)$r['views'];
    $buckets[$k]['dur_sum'] += (int)$r['dur_sum'];
    $buckets[$k]['dur_n']   += (int)$r['dur_n'];
}

$maxViews = max(1, max(array_column($buckets, 'views')));
$avgOf = fn(array $b): int => $b['dur_n'] > 0 ? (int)round($b['dur_sum'] / $b['dur_n']) : 0;
$maxAvg = max(1, max(array_map($avgOf, $buckets)));

$fmt_time = function (int $s): string {
    if ($s <= 0) return '—';
    return $s < 60 ? $s . ' s' : floor($s / 60) . ':' . str_pad((string)($s % 60), 2, '0', STR_PAD_LEFT) . ' min';
};

/* ---------- headline numbers ---------- */
$today  = (int)$pdo->query("SELECT COUNT(*) FROM views WHERE day = date('now')")->fetchColumn();
$last7  = (int)$pdo->query("SELECT COUNT(*) FROM views WHERE day >= date('now','-6 days')")->fetchColumn();
$last30 = (int)$pdo->query("SELECT COUNT(*) FROM views WHERE day >= date('now','-29 days')")->fetchColumn();

$totDurSum = array_sum(array_column($buckets, 'dur_sum'));
$totDurN   = array_sum(array_column($buckets, 'dur_n'));
$avgAll    = $totDurN > 0 ? (int)round($totDurSum / $totDurN) : 0;

$subsTotal = (int)$pdo->query('SELECT COUNT(*) FROM submissions')->fetchColumn();
$subs30    = (int)$pdo->query("SELECT COUNT(*) FROM submissions WHERE created_at >= date('now','-30 days')")->fetchColumn();
$mailFails = (int)$pdo->query('SELECT COUNT(*) FROM submissions WHERE mail_sent = 0')->fetchColumn();

$topPages = $pdo->query("SELECT path, COUNT(*) c,
                                AVG(CASE WHEN duration IS NOT NULL THEN duration END) avg_dur
                         FROM views WHERE day >= date('now','-30 days')
                         GROUP BY path ORDER BY c DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$byLang = $pdo->query("SELECT lang, COUNT(*) c FROM views WHERE day >= date('now','-30 days')
                       GROUP BY lang ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC);
$recent = $pdo->query('SELECT * FROM submissions ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);

$measured = $totDurN;

admin_header('Prehľad', 'index.php');
if ($m = flash()) echo '<div class="flash">' . esc($m) . '</div>';
?>

<div class="grid c4">
  <div class="stat-tile"><div class="num"><?= $today ?></div><div class="lbl">Návštevy dnes</div></div>
  <div class="stat-tile"><div class="num"><?= $last7 ?></div><div class="lbl">Návštevy — 7 dní</div></div>
  <div class="stat-tile"><div class="num"><?= $fmt_time($avgAll) ?></div><div class="lbl">Priemerný čas na stránke</div></div>
  <div class="stat-tile"><div class="num"><?= $subsTotal ?></div><div class="lbl">Dopyty celkom (<?= $subs30 ?> za 30 dní)</div></div>
</div>

<div class="range-bar">
  <span class="muted">Zobraziť:</span>
  <?php foreach ($RANGES as $r => $label): ?>
    <a href="index.php?range=<?= $r ?>"<?= $r === $range ? ' class="active"' : '' ?>><?= esc($label) ?></a>
  <?php endforeach; ?>
</div>

<h2>Návštevnosť — <?= esc(mb_strtolower($RANGES[$range])) ?></h2>
<div class="card">
  <div class="chart">
    <?php foreach ($buckets as $b): ?>
      <div class="bar<?= $b['views'] === 0 ? ' empty' : '' ?>"
           style="height:<?= round($b['views'] / $maxViews * 100) ?>%"
           data-tip="<?= esc($b['label']) ?>: <?= $b['views'] ?> návštev"></div>
    <?php endforeach; ?>
  </div>
  <div class="chart-axis">
    <?php $lbls = array_column($buckets, 'label'); $n = count($lbls); ?>
    <span><?= esc($lbls[0]) ?></span>
    <span><?= esc($lbls[intdiv($n, 2)]) ?></span>
    <span><?= $range === 'day' ? 'dnes' : esc(end($lbls)) ?></span>
  </div>
  <p class="muted">Merané na serveri bez cookies — bez vplyvu na cookie lištu a blokovanie reklám.</p>
</div>

<h2>Priemerný čas na stránke — <?= esc(mb_strtolower($RANGES[$range])) ?></h2>
<div class="card">
  <?php if ($measured === 0): ?>
    <p class="muted">Zatiaľ nie sú namerané žiadne časy. Čas sa zaznamená, keď návštevník stránku opustí — prvé dáta uvidíte po niekoľkých návštevách.</p>
  <?php else: ?>
    <div class="chart">
      <?php foreach ($buckets as $b): $a = $avgOf($b); ?>
        <div class="bar time<?= $a === 0 ? ' empty' : '' ?>"
             style="height:<?= round($a / $maxAvg * 100) ?>%"
             data-tip="<?= esc($b['label']) ?>: <?= $a > 0 ? $fmt_time($a) : 'bez dát' ?>"></div>
      <?php endforeach; ?>
    </div>
    <div class="chart-axis">
      <span><?= esc($lbls[0]) ?></span>
      <span><?= esc($lbls[intdiv($n, 2)]) ?></span>
      <span><?= $range === 'day' ? 'dnes' : esc(end($lbls)) ?></span>
    </div>
    <p class="muted">Priemer z <?= $measured ?> meraných zobrazení. Meria sa čas od otvorenia stránky po jej opustenie.</p>
  <?php endif; ?>
</div>

<div class="grid c2">
  <div class="card">
    <h2 style="margin-top:0;">Najnavštevovanejšie stránky (30 dní)</h2>
    <table>
      <tr><th>Stránka</th><th style="text-align:right;">Návštevy</th><th style="text-align:right;">Priem. čas</th></tr>
      <?php foreach ($topPages as $r): ?>
        <tr>
          <td><?= esc($r['path']) ?></td>
          <td style="text-align:right;"><?= (int)$r['c'] ?></td>
          <td style="text-align:right;"><?= $r['avg_dur'] ? $fmt_time((int)round($r['avg_dur'])) : '—' ?></td>
        </tr>
      <?php endforeach; if (!$topPages): ?>
        <tr><td class="muted">Zatiaľ žiadne dáta.</td></tr>
      <?php endif; ?>
    </table>
  </div>
  <div class="card">
    <h2 style="margin-top:0;">Návštevy podľa jazyka (30 dní)</h2>
    <table>
      <?php foreach ($byLang as $r): ?>
        <tr><td><?= esc(strtoupper($r['lang'])) ?></td><td style="text-align:right;"><?= (int)$r['c'] ?></td></tr>
      <?php endforeach; if (!$byLang): ?>
        <tr><td class="muted">Zatiaľ žiadne dáta.</td></tr>
      <?php endif; ?>
    </table>
    <?php if ($mailFails > 0): ?>
      <p class="bad" style="margin-top:14px;">⚠ <?= $mailFails ?> dopytov sa nepodarilo odoslať e-mailom — sú uložené v sekcii Dopyty.</p>
    <?php endif; ?>
  </div>
</div>

<h2>Posledné dopyty</h2>
<div class="card">
  <table>
    <tr><th>Dátum</th><th>Meno / spoločnosť</th><th>Typ paliet</th><th>Jazyk</th><th>E-mail odoslaný</th></tr>
    <?php foreach ($recent as $s): ?>
      <tr>
        <td><?= esc($s['created_at']) ?></td>
        <td><a href="inbox.php?id=<?= (int)$s['id'] ?>"><?= esc($s['name']) ?> — <?= esc($s['company']) ?></a></td>
        <td><?= esc($s['type']) ?></td>
        <td><?= esc(strtoupper($s['lang'])) ?></td>
        <td><?= $s['mail_sent'] ? '<span class="ok">áno</span>' : '<span class="bad">nie</span>' ?></td>
      </tr>
    <?php endforeach; if (!$recent): ?>
      <tr><td colspan="5" class="muted">Zatiaľ žiadne dopyty.</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php admin_footer();
