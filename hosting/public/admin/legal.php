<?php
// Admin — legal documents editor (per document, per language)
require_once __DIR__ . '/lib-admin.php';
require_login();

$pdo = db();
$docNames = [
    'obchodne-podmienky' => 'Obchodné podmienky',
    'gdpr' => 'Ochrana osobných údajov (GDPR)',
    'cookies' => 'Cookies politika',
    'pravne-informacie' => 'Právne informácie',
];
$slug = $_GET['doc'] ?? 'obchodne-podmienky';
if (!isset($docNames[$slug])) $slug = 'obchodne-podmienky';
$lang = $_GET['lang'] ?? 'sk';
if (!in_array($lang, LANGS, true)) $lang = 'sk';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_csrf();
    $pdo->prepare('UPDATE legal SET title = ?, h1 = ?, "desc" = ?, metaline = ?, html = ? WHERE slug = ? AND lang = ?')
        ->execute([
            clean_utf8(trim($_POST['title'] ?? '')), clean_utf8(trim($_POST['h1'] ?? '')),
            clean_utf8(trim($_POST['desc'] ?? '')), clean_utf8(trim($_POST['metaline'] ?? '')),
            clean_utf8(trim($_POST['html'] ?? '')), $slug, $lang,
        ]);
    log_activity("Uložený právny dokument '$slug' ($lang)");
    flash('Dokument bol uložený.');
    header("Location: legal.php?doc=$slug&lang=$lang");
    exit;
}

$st = $pdo->prepare('SELECT * FROM legal WHERE slug = ? AND lang = ?');
$st->execute([$slug, $lang]);
$doc = $st->fetch(PDO::FETCH_ASSOC) ?: ['title' => '', 'h1' => '', 'desc' => '', 'metaline' => '', 'html' => ''];

$langPrefix = $lang === 'sk' ? '' : $lang . '/';

admin_header('Právne dokumenty', 'legal.php');
if ($m = flash()) echo '<div class="flash">' . esc($m) . '</div>';
?>

<div class="tabs">
  <?php foreach ($docNames as $s => $label): ?>
    <a href="legal.php?doc=<?= $s ?>&amp;lang=<?= $lang ?>"<?= $s === $slug ? ' class="active"' : '' ?>><?= esc($label) ?></a>
  <?php endforeach; ?>
</div>
<div class="tabs">
  <?php foreach (ADMIN_LANGS as $l => $lbl): ?>
    <a href="legal.php?doc=<?= $slug ?>&amp;lang=<?= $l ?>"<?= $l === $lang ? ' class="active"' : '' ?>><?= $lbl ?></a>
  <?php endforeach; ?>
</div>

<form method="post">
  <?= csrf_field() ?>
  <div class="card">
    <div class="grid c2">
      <div>
        <label for="title">Názov dokumentu (v menu a titulku)</label>
        <input type="text" id="title" name="title" value="<?= esc($doc['title']) ?>">
      </div>
      <div>
        <label for="h1">Nadpis stránky (môže obsahovať HTML)</label>
        <input type="text" id="h1" name="h1" value="<?= esc($doc['h1']) ?>">
      </div>
      <div>
        <label for="desc">Meta popis (pre vyhľadávače)</label>
        <input type="text" id="desc" name="desc" value="<?= esc($doc['desc']) ?>">
      </div>
      <div>
        <label for="metaline">Riadok platnosti (nad dokumentom)</label>
        <input type="text" id="metaline" name="metaline" value="<?= esc($doc['metaline']) ?>">
      </div>
    </div>
    <p style="margin-top:16px;">
      <label for="html">Obsah dokumentu (HTML — nadpisy &lt;h2&gt;, odseky &lt;p&gt;, zoznamy &lt;ul&gt;&lt;li&gt;)</label>
      <textarea class="code" id="html" name="html"><?= esc($doc['html']) ?></textarea>
    </p>
  </div>
  <div class="savebar">
    <button class="btn" type="submit">Uložiť dokument</button>
    <a class="btn ghost" href="/<?= $langPrefix . $slug ?>.html" target="_blank" rel="noopener">Zobraziť na webe</a>
  </div>
</form>

<?php admin_footer();
