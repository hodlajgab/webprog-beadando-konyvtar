<?php
declare(strict_types=1);

/**
 * Egyszerű útvonal-kezelő a front-controller mintához.
 *
 * Az útvonal-mintákban szerepelhet "{nev}" placeholder, ami egy útvonal-szegmenset
 * fog meg (pl. "/konyvek/{id}/szerkesztes"). A megtalált értékek paraméterként
 * átadódnak a controller akciójának.
 */
final class Router
{
    /** @var array<int, array{metodus: string, minta: string, controller: string, akcio: string}> */
    private array $utvonalak = [];

    private PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
        $this->utvonalakRegisztracio();
    }

    private function utvonalakRegisztracio(): void
    {
        // Főoldal
        $this->hozzaad('GET',  '/',                            'Controllers\\FoooldalController',  'mutat');

        // Belépés / regisztráció / kilépés
        $this->hozzaad('GET',  '/belepes',                     'Controllers\\AuthController',      'belepesUrlap');
        $this->hozzaad('POST', '/belepes',                     'Controllers\\AuthController',      'belepes');
        $this->hozzaad('POST', '/regisztracio',                'Controllers\\AuthController',      'regisztracio');
        $this->hozzaad('GET',  '/kilepes',                     'Controllers\\AuthController',      'kilepes');

        // Képek
        $this->hozzaad('GET',  '/kepek',                       'Controllers\\KepekController',     'lista');
        $this->hozzaad('GET',  '/kepek/feltoltes',             'Controllers\\KepekController',     'feltoltesUrlap');
        $this->hozzaad('POST', '/kepek/feltoltes',             'Controllers\\KepekController',     'feltoltes');

        // Kapcsolat
        $this->hozzaad('GET',  '/kapcsolat',                   'Controllers\\KapcsolatController', 'urlap');
        $this->hozzaad('POST', '/kapcsolat',                   'Controllers\\KapcsolatController', 'kuldes');

        // Üzenetek (csak belépve)
        $this->hozzaad('GET',  '/uzenetek',                    'Controllers\\UzenetekController',  'lista');

        // CRUD: Könyvek
        $this->hozzaad('GET',  '/konyvek',                     'Controllers\\KonyvekController',   'lista');
        $this->hozzaad('GET',  '/konyvek/uj',                  'Controllers\\KonyvekController',   'ujUrlap');
        $this->hozzaad('POST', '/konyvek',                     'Controllers\\KonyvekController',   'letrehoz');
        $this->hozzaad('GET',  '/konyvek/{id}/szerkesztes',    'Controllers\\KonyvekController',   'szerkesztesUrlap');
        $this->hozzaad('POST', '/konyvek/{id}/szerkesztes',    'Controllers\\KonyvekController',   'frissit');
        $this->hozzaad('POST', '/konyvek/{id}/torles',         'Controllers\\KonyvekController',   'torles');
    }

    private function hozzaad(string $metodus, string $minta, string $controller, string $akcio): void
    {
        $this->utvonalak[] = [
            'metodus'    => $metodus,
            'minta'      => $minta,
            'controller' => $controller,
            'akcio'      => $akcio,
        ];
    }

    public function kezel(string $metodus, string $utvonal): void
    {
        // Záró perjel eltávolítása (kivéve a gyökér útvonalnál)
        if ($utvonal !== '/' && substr($utvonal, -1) === '/') {
            $utvonal = rtrim($utvonal, '/');
        }

        foreach ($this->utvonalak as $ut) {
            if ($ut['metodus'] !== $metodus) {
                continue;
            }
            $regex = $this->mintaRegex($ut['minta']);
            if (preg_match($regex, $utvonal, $talalat)) {
                $parameterek = array_filter(
                    $talalat,
                    fn($kulcs) => is_string($kulcs),
                    ARRAY_FILTER_USE_KEY
                );

                $osztaly = $ut['controller'];
                $controller = new $osztaly($this->dbh);
                $controller->{$ut['akcio']}($parameterek);
                return;
            }
        }

        // Nincs egyező útvonal
        http_response_code(404);
        $this->hibaOldal(404, 'A keresett oldal nem található.');
    }

    private function mintaRegex(string $minta): string
    {
        // {nev} → (?P<nev>[^/]+)
        $regex = preg_replace('#\{([a-zA-Z_]+)\}#', '(?P<$1>[^/]+)', $minta);
        return '#^' . $regex . '$#';
    }

    private function hibaOldal(int $statusz, string $uzenet): void
    {
        $cim = "Hiba {$statusz}";
        $tartalom = '<h1>' . htmlspecialchars($cim, ENT_QUOTES, 'UTF-8') . '</h1>'
                  . '<p>' . htmlspecialchars($uzenet, ENT_QUOTES, 'UTF-8') . '</p>';
        require __DIR__ . '/Views/layout.php';
    }
}
