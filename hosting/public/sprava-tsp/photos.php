<?php
// Admin — photo manager (replace or add images; auto-resize, never upscale)
require_once __DIR__ . '/lib-admin.php';
require_login();

$imgDir = dirname(__DIR__) . '/assets/img';
$maxW = 1920;

function process_upload(array $file, string $targetPath): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) return 'Nahrávanie zlyhalo (kód ' . $file['error'] . ').';
    $info = @getimagesize($file['tmp_name']);
    if (!$info || !in_array($info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)) {
        return 'Podporované sú iba fotky JPG a PNG.';
    }
    $img = $info[2] === IMAGETYPE_JPEG
        ? @imagecreatefromjpeg($file['tmp_name'])
        : @imagecreatefrompng($file['tmp_name']);
    if (!$img) return 'Súbor sa nepodarilo načítať.';
    $w = imagesx($img); $h = imagesy($img);
    global $maxW;
    if ($w > $maxW) { // downscale only — never upscale
        $nw = $maxW; $nh = (int)round($h * $maxW / $w);
        $resized = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $nw, $nh, $w, $h);
        imagedestroy($img);
        $img = $resized;
    }
    $ext = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    $ok = ($ext === 'png') ? imagepng($img, $targetPath, 6) : imagejpeg($img, $targetPath, 82);
    imagedestroy($img);
    return $ok ? null : 'Uloženie súboru zlyhalo.';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_csrf();
    if (isset($_POST['replace_target'], $_FILES['photo'])) {
        $target = basename($_POST['replace_target']);
        $path = $imgDir . '/' . $target;
        if (!file_exists($path)) {
            flash('Súbor neexistuje.');
        } else {
            $err = process_upload($_FILES['photo'], $path);
            if ($err) { flash('Chyba: ' . $err); }
            else {
                log_activity("Vymenená fotka '$target'");
                flash("Fotka '$target' bola vymenená. Ak stále vidíte starú, obnovte stránku (Ctrl+F5).");
            }
        }
    } elseif (isset($_FILES['newphoto']) && $_FILES['newphoto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $name = preg_replace('/[^a-zA-Z0-9._-]/', '-', basename($_FILES['newphoto']['name']));
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            flash('Chyba: podporované sú iba JPG a PNG.');
        } elseif (file_exists($imgDir . '/' . $name)) {
            flash('Chyba: súbor s týmto názvom už existuje — použite „vymeniť" pri danej fotke.');
        } else {
            $err = process_upload($_FILES['newphoto'], $imgDir . '/' . $name);
            if ($err) { flash('Chyba: ' . $err); }
            else { log_activity("Nahraná nová fotka '$name'"); flash("Fotka '$name' bola nahraná."); }
        }
    }
    header('Location: photos.php');
    exit;
}

$files = [];
foreach (scandir($imgDir) as $f) {
    if (preg_match('/\.(jpe?g|png)$/i', $f)) {
        $size = @getimagesize($imgDir . '/' . $f);
        $files[] = ['name' => $f, 'w' => $size[0] ?? 0, 'h' => $size[1] ?? 0,
                    'kb' => (int)round(filesize($imgDir . '/' . $f) / 1024)];
    }
}

admin_header('Fotky', 'photos.php');
if ($m = flash()) echo '<div class="flash">' . esc($m) . '</div>';
?>

<div class="card">
  <h2 style="margin-top:0;">Nahrať novú fotku</h2>
  <form method="post" enctype="multipart/form-data" style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
    <?= csrf_field() ?>
    <div><label>Súbor (JPG / PNG)</label><input type="file" name="newphoto" accept=".jpg,.jpeg,.png" required></div>
    <button class="btn small" type="submit">Nahrať</button>
  </form>
  <p class="muted" style="margin-top:10px;">Fotky sa automaticky zmenšia na max. šírku 1920 px a skomprimujú. Nikdy sa nezväčšujú.</p>
</div>

<div class="photo-grid">
  <?php foreach ($files as $f): ?>
    <div class="photo-card">
      <img src="/assets/img/<?= esc($f['name']) ?>?v=<?= time() ?>" alt="" loading="lazy">
      <div class="ph-body">
        <strong><?= esc($f['name']) ?></strong>
        <?= $f['w'] ?> × <?= $f['h'] ?> px · <?= $f['kb'] ?> KB
        <form method="post" enctype="multipart/form-data" style="margin-top:8px;">
          <?= csrf_field() ?>
          <input type="hidden" name="replace_target" value="<?= esc($f['name']) ?>">
          <input type="file" name="photo" accept=".jpg,.jpeg,.png" required style="font-size:11px; width:100%;">
          <button class="btn ghost small" type="submit" style="margin-top:6px;">Vymeniť</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php admin_footer();
