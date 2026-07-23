<?php
// Receives the time-on-page beacon and stores it against the recorded view.
require_once __DIR__ . '/lib.php';

$id = (int)($_POST['id'] ?? 0);
$s  = (int)($_POST['s'] ?? 0);

if ($id > 0 && $s >= 1 && $s <= 3600) {
    try {
        // duration IS NULL keeps the first (honest) report and ignores repeats
        db()->prepare('UPDATE views SET duration = ? WHERE id = ? AND duration IS NULL')
            ->execute([$s, $id]);
    } catch (Throwable $e) { /* never surface tracking errors */ }
}

http_response_code(204);
