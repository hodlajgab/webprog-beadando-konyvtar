<?php
/**
 * Front-controller belépési pont.
 *
 * Minden HTTP-kérés ide érkezik (lásd public/.htaccess), majd
 * a Router választja ki a megfelelő controller-akciót.
 */

declare(strict_types=1);

// Hibakezelés: éles módban semmit ne mutassunk a felhasználónak,
// de mindent logoljunk a szerver hibanaplójába.
$ejtizem = ($_SERVER['HTTP_HOST'] ?? '') === 'localhost'
        || str_starts_with($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1');
ini_set('display_errors', $ejtizem ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// Biztonsági fejlécek
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Session cookie flag-ek a session_start() ELŐTT
$httpsAktiv = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $httpsAktiv,
    'httponly' => true,
    'samesite' => 'Lax',
]);

session_start();

// Egyszerű autoloader: src/ alól tölt be, mappa-struktúrával
spl_autoload_register(function (string $osztaly): void {
    $utvonal = __DIR__ . '/../src/' . str_replace('\\', '/', $osztaly) . '.php';
    if (file_exists($utvonal)) {
        require $utvonal;
    }
});

// Adatbázis-kapcsolat (PDO)
$dbh = require __DIR__ . '/../config/db.php';

// Útvonal-kérés (URL útvonalrész, query string nélkül)
$utvonal = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$metodus = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');

$router = new Router($dbh);
$router->kezel($metodus, $utvonal);
