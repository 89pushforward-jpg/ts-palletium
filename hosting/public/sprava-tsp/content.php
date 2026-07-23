<?php
// Admin — website texts editor (all four languages side by side)
require_once __DIR__ . '/lib-admin.php';
require_login();

$pdo = db();
$page = $_GET['page'] ?? 'index';
if (!isset(ADMIN_PAGES[$page])) $page = 'index';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_csrf();
    $up = $pdo->prepare('UPDATE content SET value = ? WHERE key = ? AND lang = ?');
    $n = 0;
    foreach ($_POST['txt'] ?? [] as $key => $langs) {
        foreach ($langs as $lang => $value) {
            if (!in_array($lang, LANGS, true)) continue;
            $up->execute([simple_to_text($value), $key, $lang]);
            $n += $up->rowCount();
        }
    }
    log_activity("Uložené texty — stránka '$page'");
    flash('Texty boli uložené.');
    header('Location: content.php?page=' . urlencode($page));
    exit;
}

// keys for this page, ordered as they appear.
// Texts that sit on every page (menu, footer, cookie bar) belong to the shared
// tab only — otherwise they would fill the top of every page tab and switching
// tabs would look like nothing happened.
$rows = $pdo->query('SELECT m.key, m.label, m.pages, m.ord FROM content_meta m ORDER BY m.ord')->fetchAll(PDO::FETCH_ASSOC);
$PAGE_TABS = ['index', 'o-spolocnosti', 'produkty', 'dopyt', 'kontakt'];

$is_shared = function (array $p) use ($PAGE_TABS): bool {
    foreach ($PAGE_TABS as $t) {
        if (!in_array($t, $p, true)) return false;
    }
    return true;
};

$bucket = function (array $r) use ($is_shared, $PAGE_TABS): array {
    $p = explode(',', $r['pages'] ?? '');
    if (in_array('system', $p, true)) return ['system'];
    if ($is_shared($p)) return ['legal-chrome'];
    return array_values(array_intersect($PAGE_TABS, $p));
};

$counts = array_fill_keys(array_keys(ADMIN_PAGES), 0);
$keys = [];
foreach ($rows as $r) {
    foreach ($bucket($r) as $tab) {
        if (isset($counts[$tab])) $counts[$tab]++;
    }
    if (in_array($page, $bucket($r), true)) $keys[] = $r;
}

$vals = [];
$st = $pdo->query('SELECT key, lang, value FROM content');
foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $vals[$r['key']][$r['lang']] = $r['value'];
}

admin_header('Texty webu', 'content.php');
if ($m = flash()) echo '<div class="flash">' . esc($m) . '</div>';
?>

<div class="tabs">
  <?php foreach (ADMIN_PAGES as $p => $label): ?>
    <a href="content.php?page=<?= $p ?>"<?= $p === $page ? ' class="active"' : '' ?>><?= esc($label) ?> <span class="tab-count"><?= (int)($counts[$p] ?? 0) ?></span></a>
  <?php endforeach; ?>
</div>

<h2 style="margin-top:0;">Upravujete: <?= esc(ADMIN_PAGES[$page]) ?> — <?= count($keys) ?> textov</h2>

<p class="muted">Píšte bežný text. Ak chcete časť textu <span style="color:var(--gold-light);">zlatou farbou</span>, dajte ju medzi hviezdičky: <code style="color:var(--gold-light);">*takto*</code>. Nový riadok v políčku = nový riadok na webe. Zmeny sa na webe prejavia okamžite po uložení.</p>

<form method="post">
  <?= csrf_field() ?>
  <div class="card" style="margin-top:16px;">
    <?php foreach ($keys as $k): $key = $k['key']; ?>
      <div class="content-row">
        <div class="key-label"><?= esc($k['label']) ?></div>
        <?php foreach (ADMIN_LANGS as $lang => $lbl): ?>
          <div>
            <span class="lang-tag"><?= $lbl ?></span>
            <textarea name="txt[<?= esc($key) ?>][<?= $lang ?>]"><?= esc(text_to_simple($vals[$key][$lang] ?? '')) ?></textarea>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; if (!$keys): ?>
      <p class="muted">Táto stránka nemá žiadne editovateľné texty.</p>
    <?php endif; ?>
  </div>
  <div class="savebar">
    <button class="btn" type="submit">Uložiť všetky zmeny</button>
    <a class="btn ghost" href="/" target="_blank" rel="noopener">Otvoriť web</a>
  </div>
</form>

<script>
/* auto-grow textareas */
document.querySelectorAll('.content-row textarea').forEach(function (ta) {
  var fit = function () { ta.style.height = 'auto'; ta.style.height = Math.max(56, ta.scrollHeight + 2) + 'px'; };
  ta.addEventListener('input', fit);
  fit();
});
</script>

<?php admin_footer();
