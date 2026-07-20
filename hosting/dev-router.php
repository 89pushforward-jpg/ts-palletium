<?php
// Dev router for `php -S` — mimics the production .htaccess rewrites.
// Usage: php -S localhost:8742 -t public dev-router.php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$docroot = realpath(__DIR__ . '/public');
$file = realpath($docroot . $path);

// block internal folders like Apache does
if (preg_match('#^/(data|seed|templates)(/|$)#', $path)) {
    http_response_code(403);
    exit('Forbidden');
}

if ($file !== false && str_starts_with($file, $docroot) && is_file($file)) {
    if (str_ends_with($file, '.php')) {
        chdir(dirname($file));
        require $file;
        return true;
    }
    return false; // let the built-in server stream static files
}

// directory with index.php (e.g. /admin/)
if ($file !== false && is_dir($file) && is_file($file . '/index.php')) {
    chdir($file);
    require $file . '/index.php';
    return true;
}

// everything else -> site router
chdir($docroot);
require $docroot . '/index.php';
return true;
