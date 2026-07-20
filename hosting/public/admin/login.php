<?php
require_once __DIR__ . '/lib-admin.php';

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    check_csrf();
    $pw = $_POST['password'] ?? '';
    $hash = setting('admin_password_hash');
    if ($hash !== '' && password_verify($pw, $hash)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        log_activity('Prihlásenie do administrácie');
        // initial password file is no longer needed once login works
        $pwFile = dirname(__DIR__) . '/seed/initial-password.txt';
        if (file_exists($pwFile)) { @unlink($pwFile); }
        header('Location: index.php');
        exit;
    }
    sleep(1); // slow down guessing
    log_activity('Neúspešný pokus o prihlásenie');
    $error = 'Nesprávne heslo.';
}

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Prihlásenie — TS Palletium admin</title>
<link rel="icon" type="image/png" href="/assets/img/favicon.png">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="card">
    <h1 style="margin-bottom:18px;">TS <span style="color:var(--gold);">Palletium</span> admin</h1>
    <?php if ($error): ?><div class="flash" style="border-color:#7a2b2b;color:#e88;"><?= esc($error) ?></div><?php endif; ?>
    <form method="post">
      <?= csrf_field() ?>
      <label for="password">Heslo</label>
      <input type="password" id="password" name="password" required autofocus autocomplete="current-password">
      <p style="margin-top:16px;"><button class="btn" type="submit">Prihlásiť sa</button></p>
    </form>
  </div>
</div>
</body>
</html>
