<?php
// TS Palletium admin — auth, CSRF, layout
require_once dirname(__DIR__) . '/lib.php';

session_name('tspadmin');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax', 'path' => '/admin/']);
session_start();

function is_logged_in(): bool {
    return !empty($_SESSION['admin']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function check_csrf(): void {
    if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '-')) {
        http_response_code(403);
        exit('CSRF check failed');
    }
}

const ADMIN_LANGS = ['sk' => 'SK', 'cs' => 'CZ', 'en' => 'EN', 'de' => 'DE'];
const ADMIN_PAGES = [
    'index' => 'Domovská stránka',
    'o-spolocnosti' => 'O spoločnosti',
    'produkty' => 'Produkty',
    'dopyt' => 'Dopyt (formulár)',
    'kontakt' => 'Kontakt',
    'legal-chrome' => 'Spoločné prvky (menu, pätička…)',
    'system' => 'Systémové hlášky',
];

function admin_header(string $title, string $active): void {
    $items = [
        'index.php' => 'Prehľad',
        'content.php' => 'Texty',
        'legal.php' => 'Právne dokumenty',
        'photos.php' => 'Fotky',
        'inbox.php' => 'Dopyty',
        'settings.php' => 'Nastavenia',
    ];
    echo '<!DOCTYPE html><html lang="sk"><head><meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<meta name="robots" content="noindex, nofollow">';
    echo '<title>' . esc($title) . ' — TS Palletium admin</title>';
    echo '<link rel="icon" type="image/png" href="/assets/img/favicon.png">';
    echo '<link rel="stylesheet" href="admin.css"></head><body>';
    echo '<header class="a-top"><div class="a-brand">TS <span>PALLETIUM</span> — admin</div><nav>';
    foreach ($items as $href => $label) {
        $cls = $href === $active ? ' class="active"' : '';
        echo "<a href=\"$href\"$cls>" . esc($label) . '</a>';
    }
    echo '<a href="logout.php" class="logout">Odhlásiť</a></nav></header><main class="a-main">';
    echo '<h1>' . esc($title) . '</h1>';
}

function admin_footer(): void {
    echo '</main></body></html>';
}

function flash(string $msg = null): ?string {
    if ($msg !== null) {
        $_SESSION['flash'] = $msg;
        return null;
    }
    $m = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $m;
}
