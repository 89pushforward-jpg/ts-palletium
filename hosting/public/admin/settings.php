<?php
// Admin — settings: form recipient, autoreply per language, password change
require_once __DIR__ . '/lib-admin.php';
require_login();

$LANG_NAMES = ['sk' => 'Slovenčina', 'cs' => 'Čeština', 'en' => 'Angličtina', 'de' => 'Nemčina'];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_csrf();
    $section = $_POST['section'] ?? '';

    if ($section === 'email') {
        $rec = trim($_POST['recipient_email'] ?? '');
        $snd = trim($_POST['sender_email'] ?? '');
        if (!filter_var($rec, FILTER_VALIDATE_EMAIL) || !filter_var($snd, FILTER_VALIDATE_EMAIL)) {
            flash('Chyba: zadajte platné e-mailové adresy.');
        } else {
            set_setting('recipient_email', $rec);
            set_setting('sender_email', $snd);
            log_activity("Zmenené e-mailové nastavenia (príjemca: $rec)");
            flash('E-mailové nastavenia boli uložené.');
        }
    } elseif ($section === 'autoreply') {
        set_setting('autoreply_enabled', isset($_POST['autoreply_enabled']) ? '1' : '0');
        foreach (LANGS as $l) {
            set_setting('autoreply_subject_' . $l, clean_utf8(trim($_POST['subject_' . $l] ?? '')));
            set_setting('autoreply_text_' . $l, clean_utf8(trim($_POST['text_' . $l] ?? '')));
        }
        log_activity('Zmenené nastavenia autoodpovede');
        flash('Autoodpoveď bola uložená.');
    } elseif ($section === 'password') {
        $cur = $_POST['current'] ?? '';
        $new = $_POST['new1'] ?? '';
        $new2 = $_POST['new2'] ?? '';
        if (!password_verify($cur, setting('admin_password_hash'))) {
            flash('Chyba: súčasné heslo nie je správne.');
        } elseif (strlen($new) < 10) {
            flash('Chyba: nové heslo musí mať aspoň 10 znakov.');
        } elseif ($new !== $new2) {
            flash('Chyba: nové heslá sa nezhodujú.');
        } else {
            set_setting('admin_password_hash', password_hash($new, PASSWORD_DEFAULT));
            log_activity('Zmenené heslo administrátora');
            flash('Heslo bolo zmenené.');
        }
    } elseif ($section === 'backup') {
        // export everything as JSON download
        $pdo = db();
        $dump = [
            'exported_at' => date('c'),
            'content' => $pdo->query('SELECT * FROM content')->fetchAll(PDO::FETCH_ASSOC),
            'legal' => $pdo->query('SELECT * FROM legal')->fetchAll(PDO::FETCH_ASSOC),
            'settings' => array_filter(
                $pdo->query('SELECT * FROM settings')->fetchAll(PDO::FETCH_ASSOC),
                fn($r) => $r['name'] !== 'admin_password_hash'
            ),
            'submissions' => $pdo->query('SELECT * FROM submissions')->fetchAll(PDO::FETCH_ASSOC),
        ];
        log_activity('Stiahnutá záloha dát');
        header('Content-Type: application/json; charset=UTF-8');
        header('Content-Disposition: attachment; filename="tspalletium-zaloha-' . date('Y-m-d') . '.json"');
        echo json_encode($dump, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE);
        exit;
    }
    header('Location: settings.php');
    exit;
}

$activity = db()->query('SELECT * FROM activity ORDER BY id DESC LIMIT 15')->fetchAll(PDO::FETCH_ASSOC);

admin_header('Nastavenia', 'settings.php');
if ($m = flash()) echo '<div class="flash">' . esc($m) . '</div>';
?>

<div class="card">
  <h2 style="margin-top:0;">E-maily formulára</h2>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="email">
    <div class="grid c2">
      <div>
        <label for="recipient_email">Kam chodia dopyty z formulára</label>
        <input type="email" id="recipient_email" name="recipient_email" value="<?= esc(setting('recipient_email')) ?>" required>
      </div>
      <div>
        <label for="sender_email">Odosielateľ (musí byť schránka na doméne, napr. info@tspalletium.com)</label>
        <input type="email" id="sender_email" name="sender_email" value="<?= esc(setting('sender_email')) ?>" required>
      </div>
    </div>
    <p style="margin-top:14px;"><button class="btn small" type="submit">Uložiť</button></p>
  </form>
</div>

<div class="card">
  <h2 style="margin-top:0;">Automatická odpoveď zákazníkovi</h2>
  <p class="muted">Zákazník dostane odpoveď v jazyku, v ktorom vyplnil formulár.</p>
  <form method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="section" value="autoreply">
    <p style="margin:14px 0;">
      <label style="display:flex; align-items:center; gap:10px; text-transform:none; font-size:14px; color:#fff;">
        <input type="checkbox" name="autoreply_enabled" style="width:auto;" <?= setting('autoreply_enabled') === '1' ? 'checked' : '' ?>>
        Posielať automatickú odpoveď po vyplnení formulára
      </label>
    </p>
    <div class="grid c2">
      <?php foreach ($LANG_NAMES as $l => $name): ?>
        <div>
          <h2 style="margin-top:6px;"><?= esc($name) ?> (<?= strtoupper($l === 'cs' ? 'cz' : $l) ?>)</h2>
          <label for="subject_<?= $l ?>">Predmet</label>
          <input type="text" id="subject_<?= $l ?>" name="subject_<?= $l ?>" value="<?= esc(setting('autoreply_subject_' . $l)) ?>">
          <label for="text_<?= $l ?>" style="margin-top:10px;">Text odpovede</label>
          <textarea id="text_<?= $l ?>" name="text_<?= $l ?>" style="min-height:150px;"><?= esc(setting('autoreply_text_' . $l)) ?></textarea>
        </div>
      <?php endforeach; ?>
    </div>
    <p style="margin-top:14px;"><button class="btn small" type="submit">Uložiť autoodpoveď</button></p>
  </form>
</div>

<div class="grid c2">
  <div class="card">
    <h2 style="margin-top:0;">Zmena hesla</h2>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="section" value="password">
      <label for="current">Súčasné heslo</label>
      <input type="password" id="current" name="current" required autocomplete="current-password">
      <label for="new1" style="margin-top:10px;">Nové heslo (min. 10 znakov)</label>
      <input type="password" id="new1" name="new1" required autocomplete="new-password">
      <label for="new2" style="margin-top:10px;">Nové heslo znova</label>
      <input type="password" id="new2" name="new2" required autocomplete="new-password">
      <p style="margin-top:14px;"><button class="btn small" type="submit">Zmeniť heslo</button></p>
    </form>
  </div>
  <div class="card">
    <h2 style="margin-top:0;">Záloha dát</h2>
    <p class="muted" style="margin-bottom:14px;">Stiahne všetky texty, právne dokumenty, nastavenia a dopyty ako jeden súbor JSON.</p>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="section" value="backup">
      <button class="btn small" type="submit">Stiahnuť zálohu</button>
    </form>
    <h2>Posledná aktivita</h2>
    <table>
      <?php foreach ($activity as $a): ?>
        <tr><td class="muted" style="white-space:nowrap;"><?= esc($a['created_at']) ?></td><td><?= esc($a['action']) ?></td></tr>
      <?php endforeach; if (!$activity): ?>
        <tr><td class="muted">Žiadna aktivita.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>

<?php admin_footer();
