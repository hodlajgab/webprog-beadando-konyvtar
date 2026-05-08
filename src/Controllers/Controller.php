<?php
declare(strict_types=1);

namespace Controllers;

use PDO;

/**
 * Controller alaposztály — minden konkrét controller ebből származik.
 *
 * Tartalmazza a közös segéd-metódusokat: view renderelés, redirect,
 * bejelentkezés ellenőrzése, flash üzenet kezelés.
 */
abstract class Controller
{
    protected PDO $dbh;

    public function __construct(PDO $dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * Renderel egy view-t a közös layout-tal körülfogva.
     *
     * @param string $view  pl. 'fooldal' → src/Views/fooldal.php
     * @param array<string, mixed> $valtozok a view-ban elérhető változók
     * @param string $cim   böngésző címsoránál megjelenő szöveg
     */
    protected function nezet(string $view, array $valtozok = [], string $cim = ''): void
    {
        // CSRF token automatikusan elérhetővé tehető minden view-ban
        $valtozok['csrf_token'] = $this->csrfToken();

        // A view változói $valtozok kulcsaiból keletkeznek
        extract($valtozok, EXTR_SKIP);

        // A view a $tartalom változón keresztül kerül a layout-ba
        ob_start();
        require __DIR__ . '/../Views/' . $view . '.php';
        $tartalom = (string) ob_get_clean();

        // Layout változói
        require __DIR__ . '/../Views/layout.php';
    }

    protected function atiranyit(string $utvonal): void
    {
        header('Location: ' . $utvonal);
        exit;
    }

    /**
     * Flash üzenet (egyszer megjelenő figyelmeztetés/visszajelzés).
     */
    protected function flash(string $tipus, string $uzenet): void
    {
        $_SESSION['flash'][] = ['tipus' => $tipus, 'uzenet' => $uzenet];
    }

    protected function bejelentkezett(): bool
    {
        return isset($_SESSION['felhasznalo_id']);
    }

    /**
     * Ha nincs belépve, átirányít a belépés oldalra.
     */
    protected function csakBelepve(): void
    {
        if (!$this->bejelentkezett()) {
            $this->flash('hiba', 'Ehhez a funkcióhoz be kell jelentkezni.');
            $this->atiranyit('/belepes');
        }
    }

    /**
     * Biztonságos kimenet — XSS ellen.
     */
    protected function biztonsagos(?string $szoveg): string
    {
        return htmlspecialchars($szoveg ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * CSRF token: session-ben tárolt 64 hexa karakteres random string.
     * Ugyanaz a token marad a session élettartama alatt — egyszerűbb,
     * mint per-form-rotation, és a tipikus iskolai beadandó-szintű
     * támadási modellben elegendő.
     */
    protected function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    /**
     * POST kérésen ellenőrzi, hogy a beérkezett _csrf mező egyezik-e
     * a session-ben tárolttal. Ha nem, a kérést elvetjük és
     * visszaküldjük a felhasználót a főoldalra hibaüzenettel.
     */
    protected function csrfEllenoriz(): void
    {
        $beerkezett = (string) ($_POST['_csrf'] ?? '');
        $eltarolt   = (string) ($_SESSION['_csrf_token'] ?? '');

        if ($eltarolt === '' || !hash_equals($eltarolt, $beerkezett)) {
            http_response_code(419);
            $this->flash('hiba',
                'A biztonsági token érvénytelen vagy lejárt. Próbáld újra.');
            $this->atiranyit('/');
        }
    }
}
