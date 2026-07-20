<?php
// TS Palletium — public site router
require_once __DIR__ . '/lib.php';

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$uri = rawurldecode($uri);

// split language prefix
$lang = DEFAULT_LANG;
$rest = ltrim($uri, '/');
foreach (LANGS as $l) {
    if ($l !== DEFAULT_LANG && ($rest === $l || str_starts_with($rest, $l . '/'))) {
        $lang = $l;
        $rest = substr($rest, strlen($l));
        $rest = ltrim($rest, '/');
        break;
    }
}
if ($rest === '') {
    $rest = 'index.html';
}

$page = basename($rest); // no subdirectories in content URLs

load_lang($lang);

$slug = str_ends_with($page, '.html') ? substr($page, 0, -5) : $page;

if (in_array($page, MAIN_PAGES, true)) {
    track_view($uri, $lang);
    $LANG = $lang;
    require __DIR__ . '/templates/page-' . str_replace('.html', '.php', $page);
    exit;
}

if (in_array($slug, LEGAL_SLUGS, true)) {
    track_view($uri, $lang);
    $LANG = $lang;
    $st = db()->prepare('SELECT * FROM legal WHERE slug = ? AND lang = ?');
    $st->execute([$slug, $lang]);
    $DOC = $st->fetch(PDO::FETCH_ASSOC);
    if (!$DOC) {
        $st->execute([$slug, DEFAULT_LANG]);
        $DOC = $st->fetch(PDO::FETCH_ASSOC);
    }
    require __DIR__ . '/templates/legal.php';
    exit;
}

// unknown page -> home of current language
http_response_code(302);
header('Location: ' . lang_url($lang, 'index.html'));
exit;
