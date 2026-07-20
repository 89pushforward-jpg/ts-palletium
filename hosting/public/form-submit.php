<?php
// TS Palletium — quote form endpoint (stores submission + notifies + optional autoreply)
require_once __DIR__ . '/lib.php';

header('Content-Type: application/json; charset=UTF-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed']);
    exit;
}

$lang = $_POST['lang'] ?? DEFAULT_LANG;
if (!in_array($lang, LANGS, true)) {
    $lang = DEFAULT_LANG;
}
load_lang($lang);

$f = fn(string $k): string => clean_utf8(trim((string)($_POST[$k] ?? '')));
$name    = $f('name');
$company = $f('company');
$email   = $f('email');
$phone   = $f('phone');
$type    = $f('type');
$qty     = $f('qty');
$place   = $f('place');
$message = $f('message');

if ($name === '' || $company === '' || $type === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'message' => t('form.invalid')]);
    exit;
}

// store
db()->prepare('INSERT INTO submissions (created_at, lang, name, company, email, phone, type, qty, place, message)
               VALUES (?,?,?,?,?,?,?,?,?,?)')
    ->execute([date('Y-m-d H:i:s'), $lang, $name, $company, $email, $phone, $type, $qty, $place, $message]);
$id = (int)db()->lastInsertId();

// notify site owner
$recipient = setting('recipient_email', 'info@tspalletium.com');
$sender    = setting('sender_email', 'info@tspalletium.com');
$body = "Novy dopyt z webu tspalletium.com (#$id, jazyk: $lang)\n\n"
      . "Meno: $name\nSpolocnost: $company\nE-mail: $email\nTelefon: $phone\n"
      . "Typ paliet: $type\nMnozstvo: $qty\nMiesto dodania: $place\n\nSprava:\n$message\n";
$sent = send_mail($recipient, 'Dopyt z webu — ' . ($company !== '' ? $company : $name), $body, $sender);

// autoreply to the customer in their language
$autoSent = 0;
if (setting('autoreply_enabled', '0') === '1') {
    $subject = setting('autoreply_subject_' . $lang, setting('autoreply_subject_sk'));
    $text    = setting('autoreply_text_' . $lang, setting('autoreply_text_sk'));
    if ($subject !== '' && $text !== '') {
        $autoSent = send_mail($email, $subject, $text, $sender) ? 1 : 0;
    }
}

db()->prepare('UPDATE submissions SET mail_sent = ?, autoreply_sent = ? WHERE id = ?')
    ->execute([$sent ? 1 : 0, $autoSent, $id]);

echo json_encode(['ok' => true, 'message' => t('form.success')]);
