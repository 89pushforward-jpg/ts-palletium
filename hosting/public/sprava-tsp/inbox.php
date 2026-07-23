<?php
// Admin — form submissions inbox
require_once __DIR__ . '/lib-admin.php';
require_login();

$pdo = db();

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['delete_id'])) {
    check_csrf();
    $pdo->prepare('DELETE FROM submissions WHERE id = ?')->execute([(int)$_POST['delete_id']]);
    log_activity('Zmazaný dopyt #' . (int)$_POST['delete_id']);
    flash('Dopyt bol zmazaný.');
    header('Location: inbox.php');
    exit;
}

$detail = null;
if (isset($_GET['id'])) {
    $st = $pdo->prepare('SELECT * FROM submissions WHERE id = ?');
    $st->execute([(int)$_GET['id']]);
    $detail = $st->fetch(PDO::FETCH_ASSOC);
}

$subs = $pdo->query('SELECT * FROM submissions ORDER BY id DESC LIMIT 200')->fetchAll(PDO::FETCH_ASSOC);

admin_header('Dopyty z formulára', 'inbox.php');
if ($m = flash()) echo '<div class="flash">' . esc($m) . '</div>';

if ($detail): ?>
  <div class="card">
    <h2 style="margin-top:0;">Dopyt #<?= (int)$detail['id'] ?> — <?= esc($detail['created_at']) ?> (<?= esc(strtoupper($detail['lang'])) ?>)</h2>
    <table>
      <tr><th>Meno</th><td><?= esc($detail['name']) ?></td></tr>
      <tr><th>Spoločnosť</th><td><?= esc($detail['company']) ?></td></tr>
      <tr><th>E-mail</th><td><a href="mailto:<?= esc($detail['email']) ?>"><?= esc($detail['email']) ?></a></td></tr>
      <tr><th>Telefón</th><td><?= esc($detail['phone']) ?></td></tr>
      <tr><th>Typ paliet</th><td><?= esc($detail['type']) ?></td></tr>
      <tr><th>Množstvo</th><td><?= esc($detail['qty']) ?></td></tr>
      <tr><th>Miesto dodania</th><td><?= esc($detail['place']) ?></td></tr>
      <tr><th>Správa</th><td><?= nl2br(esc($detail['message'])) ?></td></tr>
      <tr><th>Notifikačný e-mail</th><td><?= $detail['mail_sent'] ? '<span class="ok">odoslaný</span>' : '<span class="bad">neodoslaný</span>' ?></td></tr>
      <tr><th>Autoodpoveď</th><td><?= $detail['autoreply_sent'] ? '<span class="ok">odoslaná</span>' : '<span class="muted">nie</span>' ?></td></tr>
    </table>
    <p style="margin-top:16px; display:flex; gap:10px;">
      <a class="btn small" href="mailto:<?= esc($detail['email']) ?>?subject=<?= rawurlencode('TS Palletium — vaša cenová ponuka') ?>">Odpovedať e-mailom</a>
      <a class="btn ghost small" href="inbox.php">Späť na zoznam</a>
      <form method="post" onsubmit="return confirm('Naozaj zmazať tento dopyt?');" style="display:inline;">
        <?= csrf_field() ?>
        <input type="hidden" name="delete_id" value="<?= (int)$detail['id'] ?>">
        <button class="btn danger small" type="submit">Zmazať</button>
      </form>
    </p>
  </div>
<?php endif; ?>

<div class="card">
  <table>
    <tr><th>#</th><th>Dátum</th><th>Meno / spoločnosť</th><th>Typ</th><th>Množstvo</th><th>Jazyk</th><th>E-mail</th></tr>
    <?php foreach ($subs as $s): ?>
      <tr>
        <td><?= (int)$s['id'] ?></td>
        <td><?= esc($s['created_at']) ?></td>
        <td><a href="inbox.php?id=<?= (int)$s['id'] ?>"><?= esc($s['name']) ?> — <?= esc($s['company']) ?></a></td>
        <td><?= esc($s['type']) ?></td>
        <td><?= esc($s['qty']) ?></td>
        <td><?= esc(strtoupper($s['lang'])) ?></td>
        <td><?= $s['mail_sent'] ? '<span class="ok">✓</span>' : '<span class="bad">✗</span>' ?></td>
      </tr>
    <?php endforeach; if (!$subs): ?>
      <tr><td colspan="7" class="muted">Zatiaľ žiadne dopyty.</td></tr>
    <?php endif; ?>
  </table>
</div>

<?php admin_footer();
