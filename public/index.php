<?php
/**
 * Front-controller belépési pont.
 *
 * Minden HTTP-kérés ide érkezik (lásd public/.htaccess), majd
 * a Router választja ki a megfelelő controller-akciót.
 */

declare(strict_types=1);

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
