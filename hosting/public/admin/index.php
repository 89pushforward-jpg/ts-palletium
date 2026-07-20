<?php
// Admin dashboard — traffic statistics + recent enquiries
require_once __DIR__ . '/lib-admin.php';
require_login();

$pdo = db();

// 30-day series
$days = [];
for ($i = 29; $i >= 0; $i--) {
    $days[date('Y-m-d', strtotime("-$i days"))] = 0;
}
$st = $pdo->prepare("SELECT day, COUNT(*) c FROM views WHERE day >= ? GROUP BY day");
$st->execute([array_key_first($days)]);
foreach ($st->fetchAll(PDO::FETCH_KEY_PAIR) as $d => $c) {
    if (isset($days[$d])) $days[$d] = (int)$c;
}
$max = max(1, max($days));

$today = $days[date('Y-m-d')] ?? 0;
$last7 = array_sum(array_slice($days, -7, 7, true));
$last30 = array_sum($days);

$subsTotal = (int)$pdo->query('SELECT COUNT(*) FROM submissions')->fetchColumn();
$subs30 = (int)$pdo->query("SELECT COUNT(*) FROM submissions WHERE created_at >= date('now','-30 days')")->fetchColumn();
$mailFails = (int)$pdo->query('SELECT COUNT(*) FROM submissions WHERE mail_sent = 0')->fetchColumn();

$topPages = $pdo->query("SELECT path, COUNT(*) c FROM views WHERE day >= date('now','-30 days')
                         GROUP BY path ORDER BY c DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
$byLang = $pdo->query("SELECT lang, COUNT(*) c FROM views WHERE day >= date('now','-30 days')
                       GROUP BY lang ORDER BY c DESC")->fetchAll(PDO::FETCH_ASSOC);
$recent = $pdo->query('SELECT * FROM submissions ORDER BY id DESC LIMIT 5')->fetchAll(PDO::FETCH_ASSOC);

admin_header('Prehľad', 'index.php');
if ($m = flash()) echo '<div class="flash">' . esc($m) . '</div>';
?>

<div class="grid c4">
  <div class="stat-tile"><div class="num"><?= $today ?></div><div class="lbl">Návštevy dnes</div></div>
  <div class="stat-tile"><div class="num"><?= $last7 ?></div><div class="lbl">Návštevy — 7 dní</div></div>
  <div class="stat-tile"><div class="num"><?= $last30 ?></div><div class="lbl">Návštevy — 30 dní</div></div>
  <div class="stat-tile"><div class="num"><?= $subsTotal ?></div><div class="lbl">Dopyty celkom (<?= $subs30 ?> za 30 dní)</div></div>
</div>

<h2>Návštevnosť za posledných 30 dní</h2>
<div class="card">
  <div class="chart">
    <?php foreach ($days as $d => $c): ?>
      <div class="bar" style="height:<?= round($c / $max * 100) ?>%" data-tip="<?= esc(date('j. n.', strtotime($d))) ?>: <?= $c ?>"></div>
    <?php endforeach; ?>
  </div>
  <p class="muted">Merané na serveri bez cookies — bez vplyvu na cookie lištu a blokovanie reklám.</p>
</div>

<div class="grid c2">
  <div class="card">
    <h2 style="margin-top:0;">Najnavštevovanejšie stránky (30 dní)</h2>
    <table>
      <?php foreach ($topPages as $r): ?>
        <tr><td><?= esc($r['path']) ?></td><td style="text-align:right;"><?= (int)$r['c'] ?></td></tr>
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
