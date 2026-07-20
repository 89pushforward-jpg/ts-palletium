<?php
// Admin — legal documents editor (visual editor, per document, per language)
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
            clean_utf8(trim($_POST['title'] ?? '')),
            simple_to_text(trim($_POST['h1'] ?? '')),
            clean_utf8(trim($_POST['desc'] ?? '')),
            clean_utf8(trim($_POST['metaline'] ?? '')),
            sanitize_rich_html($_POST['html'] ?? ''),
            $slug, $lang,
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

<form method="post" id="legalForm">
  <?= csrf_field() ?>
  <div class="card">
    <p class="muted" style="margin-bottom:14px;">Píšte ako v bežnom textovom editore — označte text a použite tlačidlá. Žiadne značky ani kódy nie sú potrebné.</p>
    <div class="wtoolbar">
      <button type="button" data-cmd="h2">Nadpis sekcie</button>
      <button type="button" data-cmd="p">Bežný odsek</button>
      <button type="button" data-cmd="ul">• Odrážky</button>
      <button type="button" data-cmd="bold"><strong>B</strong> Tučné</button>
      <button type="button" data-cmd="link">🔗 Odkaz</button>
      <button type="button" data-cmd="clear">Vyčistiť formát</button>
    </div>
    <div class="wysiwyg" id="editor" contenteditable="true"><?= $doc['html'] ?></div>
    <textarea name="html" id="htmlField" hidden></textarea>
  </div>

  <details class="card" style="padding-top:16px;">
    <summary style="cursor:pointer; color:var(--gold-light); font-weight:600;">Rozšírené nastavenia (názov, nadpis, popis pre vyhľadávače)</summary>
    <div class="grid c2" style="margin-top:16px;">
      <div>
        <label for="title">Názov dokumentu (v menu a titulku okna)</label>
        <input type="text" id="title" name="title" value="<?= esc($doc['title']) ?>">
      </div>
      <div>
        <label for="h1">Nadpis stránky (*hviezdičky* = zlatý text)</label>
        <input type="text" id="h1" name="h1" value="<?= esc(text_to_simple($doc['h1'])) ?>">
      </div>
      <div>
        <label for="desc">Popis pre vyhľadávače</label>
        <input type="text" id="desc" name="desc" value="<?= esc($doc['desc']) ?>">
      </div>
      <div>
        <label for="metaline">Riadok platnosti (zobrazuje sa nad dokumentom)</label>
        <input type="text" id="metaline" name="metaline" value="<?= esc($doc['metaline']) ?>">
      </div>
    </div>
  </details>

  <div class="savebar">
    <button class="btn" type="submit">Uložiť dokument</button>
    <a class="btn ghost" href="/<?= $langPrefix . $slug ?>.html" target="_blank" rel="noopener">Zobraziť na webe</a>
  </div>
</form>

<script>
(function () {
  var ed = document.getElementById('editor');

  document.querySelectorAll('.wtoolbar button').forEach(function (btn) {
    btn.addEventListener('click', function () {
      ed.focus();
      switch (btn.dataset.cmd) {
        case 'h2': document.execCommand('formatBlock', false, 'h2'); break;
        case 'p': document.execCommand('formatBlock', false, 'p'); break;
        case 'ul': document.execCommand('insertUnorderedList'); break;
        case 'bold': document.execCommand('bold'); break;
        case 'link':
          var url = prompt('Adresa odkazu (napr. https://… alebo info@… ako mailto:info@…):');
          if (url) document.execCommand('createLink', false, url);
          break;
        case 'clear':
          document.execCommand('removeFormat');
          document.execCommand('formatBlock', false, 'p');
          break;
      }
    });
  });

  /* paste as plain text so no foreign formatting (Word, web) leaks in */
  ed.addEventListener('paste', function (e) {
    e.preventDefault();
    var text = (e.clipboardData || window.clipboardData).getData('text/plain');
    document.execCommand('insertText', false, text);
  });

  document.getElementById('legalForm').addEventListener('submit', function () {
    document.getElementById('htmlField').value = ed.innerHTML;
  });
})();
</script>

<?php admin_footer();
