<?php
// TS Palletium — shared library (DB, seeding, translations, helpers)
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $fresh = !file_exists(DB_PATH);
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA journal_mode = WAL');
        if ($fresh) {
            seed_db($pdo);
        }
    }
    return $pdo;
}

function seed_db(PDO $pdo): void {
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS content (
            key TEXT NOT NULL, lang TEXT NOT NULL, value TEXT NOT NULL DEFAULT "",
            PRIMARY KEY (key, lang));
        CREATE TABLE IF NOT EXISTS content_meta (
            key TEXT PRIMARY KEY, label TEXT, pages TEXT, ord INTEGER);
        CREATE TABLE IF NOT EXISTS legal (
            slug TEXT NOT NULL, lang TEXT NOT NULL,
            title TEXT, h1 TEXT, "desc" TEXT, metaline TEXT, html TEXT,
            PRIMARY KEY (slug, lang));
        CREATE TABLE IF NOT EXISTS settings (name TEXT PRIMARY KEY, value TEXT);
        CREATE TABLE IF NOT EXISTS submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at TEXT NOT NULL, lang TEXT, name TEXT, company TEXT,
            email TEXT, phone TEXT, type TEXT, qty TEXT, place TEXT,
            message TEXT, mail_sent INTEGER DEFAULT 0, autoreply_sent INTEGER DEFAULT 0);
        CREATE TABLE IF NOT EXISTS views (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            day TEXT NOT NULL, path TEXT NOT NULL, lang TEXT NOT NULL);
        CREATE INDEX IF NOT EXISTS idx_views_day ON views(day);
        CREATE TABLE IF NOT EXISTS activity (
            id INTEGER PRIMARY KEY AUTOINCREMENT, created_at TEXT, action TEXT);
    ');
    $data = json_decode(file_get_contents(SEED_PATH), true);
    $insC = $pdo->prepare('INSERT OR IGNORE INTO content (key, lang, value) VALUES (?,?,?)');
    $insM = $pdo->prepare('INSERT OR IGNORE INTO content_meta (key, label, pages, ord) VALUES (?,?,?,?)');
    foreach ($data['content'] as $key => $langs) {
        foreach ($langs as $lang => $value) {
            $insC->execute([$key, $lang, $value]);
        }
        $m = $data['meta'][$key];
        $insM->execute([$key, $m['label'], implode(',', $m['pages']), $m['ord']]);
    }
    $insL = $pdo->prepare('INSERT OR IGNORE INTO legal (slug, lang, title, h1, "desc", metaline, html) VALUES (?,?,?,?,?,?,?)');
    foreach ($data['legal'] as $slug => $langs) {
        foreach ($langs as $lang => $d) {
            $insL->execute([$slug, $lang, $d['title'], $d['h1'], $d['desc'], $d['metaline'], $d['html']]);
        }
    }
    $insS = $pdo->prepare('INSERT OR IGNORE INTO settings (name, value) VALUES (?,?)');
    foreach ($data['defaults'] as $name => $value) {
        $insS->execute([$name, $value]);
    }
    // initial admin password comes from seed/initial-password.txt (deleted after first login)
    $pwFile = __DIR__ . '/seed/initial-password.txt';
    if (file_exists($pwFile)) {
        $pw = trim(file_get_contents($pwFile));
        $insS->execute(['admin_password_hash', password_hash($pw, PASSWORD_DEFAULT)]);
    }
}

function setting(string $name, string $default = ''): string {
    static $cache = null;
    if ($cache === null) {
        $cache = db()->query('SELECT name, value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    return $cache[$name] ?? $default;
}

function set_setting(string $name, string $value): void {
    db()->prepare('INSERT INTO settings (name, value) VALUES (?,?)
                   ON CONFLICT(name) DO UPDATE SET value = excluded.value')
        ->execute([$name, $value]);
}

/* ---------- public site helpers ---------- */

$GLOBALS['LANG'] = DEFAULT_LANG;
$GLOBALS['T'] = [];

function load_lang(string $lang): void {
    $GLOBALS['LANG'] = $lang;
    $st = db()->prepare('SELECT key, value FROM content WHERE lang = ?');
    $st->execute([$lang]);
    $GLOBALS['T'] = $st->fetchAll(PDO::FETCH_KEY_PAIR);
    if ($lang !== DEFAULT_LANG) {
        $st->execute([DEFAULT_LANG]);
        foreach ($st->fetchAll(PDO::FETCH_KEY_PAIR) as $k => $v) {
            if (!isset($GLOBALS['T'][$k]) || $GLOBALS['T'][$k] === '') {
                $GLOBALS['T'][$k] = $v; // fallback to Slovak
            }
        }
    }
}

function t(string $key): string {
    return $GLOBALS['T'][$key] ?? '';
}

function lang_url(string $lang, string $page): string {
    $prefix = $lang === DEFAULT_LANG ? '/' : '/' . $lang . '/';
    return $prefix . ($page === 'index.html' && $lang === DEFAULT_LANG ? '' : $page);
}

function lang_switcher(string $page): string {
    $out = '    <div class="lang-switch" aria-label="Language">' . "\n";
    foreach (LANGS as $l) {
        $label = $l === 'cs' ? 'CZ' : strtoupper($l);
        $cls = $l === $GLOBALS['LANG'] ? ' class="active"' : '';
        $out .= '      <a href="' . lang_url($l, $page) . '"' . $cls . '>' . $label . "</a>\n";
    }
    return $out . "    </div>\n";
}

function hreflangs(string $page): string {
    $out = '';
    foreach (LANGS as $l) {
        $out .= '<link rel="alternate" hreflang="' . $l . '" href="' . BASE_URL . lang_url($l, $page) . "\">\n";
    }
    $out .= '<link rel="alternate" hreflang="x-default" href="' . BASE_URL . lang_url(DEFAULT_LANG, $page) . "\">\n";
    return $out;
}

function esc(?string $s): string {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

/* Drop invalid byte sequences so stored data is always valid UTF-8 */
function clean_utf8(string $s): string {
    return mb_check_encoding($s, 'UTF-8') ? $s : mb_convert_encoding($s, 'UTF-8', 'UTF-8');
}

/* ---------- editor-friendly text conversion ----------
   Admins edit short texts in a plain format:
     *zlatý text*     ->  <span class="accent">zlatý text</span>
     new line         ->  <br>
     [text](odkaz)    ->  <a href="odkaz">text</a>
   Stored form is always HTML; these two functions convert both ways. */

function text_to_simple(string $html): string {
    $s = $html;
    $s = preg_replace('#<span class="accent">(.*?)</span>#s', '*$1*', $s);
    $s = preg_replace('#<a href="([^"]*)">(.*?)</a>#s', '[$2]($1)', $s);
    $s = preg_replace('#<br\s*/?>#i', "\n", $s);
    $s = strip_tags($s);
    return html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function simple_to_text(string $simple): string {
    $s = str_replace("\r", '', clean_utf8($simple));
    $s = htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    $s = preg_replace('#\*([^*\n][^*]*)\*#', '<span class="accent">$1</span>', $s);
    $s = preg_replace_callback('#\[([^\]]+)\]\(([^)\s]+)\)#', function ($m) {
        $url = $m[2];
        if (!preg_match('#^(https?://|mailto:|tel:|/|[a-z0-9-]+\.html)#i', $url)) {
            return $m[1]; // unknown scheme -> keep the text only
        }
        return '<a href="' . $url . '">' . $m[1] . '</a>';
    }, $s);
    return str_replace("\n", '<br>', $s);
}

/* Sanitize rich HTML from the visual editor: allow-listed tags only,
   attributes stripped (except safe href on links). */
function sanitize_rich_html(string $html): string {
    $s = clean_utf8($html);
    $s = preg_replace('#<(script|style)[^>]*>.*?</\1>#is', '', $s);
    $s = strip_tags($s, '<h2><h3><p><ul><ol><li><strong><b><em><i><u><a><br>');
    // normalize plain tags, drop all attributes
    $s = preg_replace('#<(h2|h3|p|ul|ol|li|strong|b|em|i|u)\b[^>]*>#i', '<$1>', $s);
    $s = preg_replace('#<br\b[^>]*>#i', '<br>', $s);
    // links: keep only a safe href
    $s = preg_replace_callback('#<a\b[^>]*>#i', function ($m) {
        if (preg_match('#href\s*=\s*"([^"]*)"#i', $m[0], $h)
            && preg_match('#^(https?://|mailto:|tel:|/|[a-z0-9-]+\.html)#i', $h[1])) {
            return '<a href="' . htmlspecialchars($h[1], ENT_QUOTES, 'UTF-8') . '">';
        }
        return '<a>';
    }, $s);
    // drop empty paragraphs the editor tends to leave behind
    $s = preg_replace('#<p>(\s|&nbsp;|<br>)*</p>#i', '', $s);
    return trim($s);
}

function track_view(string $path, string $lang): void {
    try {
        db()->prepare('INSERT INTO views (day, path, lang) VALUES (?,?,?)')
            ->execute([date('Y-m-d'), $path, $lang]);
    } catch (Throwable $e) { /* tracking must never break the page */ }
}

function log_activity(string $action): void {
    try {
        db()->prepare('INSERT INTO activity (created_at, action) VALUES (?,?)')
            ->execute([date('Y-m-d H:i:s'), $action]);
    } catch (Throwable $e) { }
}

/* ---------- mail ---------- */

function send_mail(string $to, string $subject, string $body, string $from): bool {
    $headers = 'From: TS Palletium <' . $from . ">\r\n"
             . 'Reply-To: ' . $from . "\r\n"
             . "MIME-Version: 1.0\r\n"
             . "Content-Type: text/plain; charset=UTF-8\r\n"
             . "Content-Transfer-Encoding: 8bit\r\n";
    $encSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    try {
        return @mail($to, $encSubject, $body, $headers);
    } catch (Throwable $e) {
        return false;
    }
}
