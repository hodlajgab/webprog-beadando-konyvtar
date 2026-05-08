<?php
/**
 * Adatbázis-kapcsolat konfiguráció.
 *
 * Helyi fejlesztéshez töltsd ki az alábbi értékeket, vagy hozz létre
 * config/db.local.php-t ugyanezekkel a változókkal — az felülírja ezt.
 *
 * Éles tárhelyen a $host, $adatbazis, $felhasznalo, $jelszo értékeit
 * a tárhely-szolgáltatótól kapott adatokra cseréld.
 */

declare(strict_types=1);

$host        = 'localhost';
$adatbazis   = 'webprog_konyvtar';
$felhasznalo = 'root';
$jelszo      = '';

// Helyi felülírás (ha létezik). NE kerüljön Git-be.
if (file_exists(__DIR__ . '/db.local.php')) {
    require __DIR__ . '/db.local.php';
}

try {
    $dbh = new PDO(
        "mysql:host={$host};dbname={$adatbazis};charset=utf8mb4",
        $felhasznalo,
        $jelszo,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    error_log('DB kapcsolat hiba: ' . $e->getMessage());
    echo 'Adatbázis-kapcsolódási hiba. Próbálja újra később.';
    exit;
}

return $dbh;
