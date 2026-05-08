<?php
declare(strict_types=1);

namespace Controllers;

/**
 * Kapcsolat-űrlap. A feladatkiírás szerint:
 *   - Kliens (JS) ÉS szerver (PHP) oldalon is validál.
 *   - HTML5 validátor attribútumokat (required, pattern stb.) NEM használ.
 *   - Sikeres küldés után az adatokat egy "5. oldal"-on visszamutatja
 *     ÉS lementi az adatbázisba.
 */
final class KapcsolatController extends Controller
{
    public function urlap(): void
    {
        $bejelentkezett = $this->bejelentkezett();
        $alapertekek = [
            'nev'    => $bejelentkezett ? trim(($_SESSION['felhasznalo_csaladi_nev'] ?? '') . ' ' . ($_SESSION['felhasznalo_uton_nev'] ?? '')) : '',
            'email'  => '',
            'targy'  => '',
            'uzenet' => '',
        ];

        $this->nezet('kapcsolat/urlap', [
            'hibak'   => [],
            'mezok'   => $alapertekek,
        ], 'Kapcsolat');
    }

    public function kuldes(): void
    {
        $mezok = [
            'nev'    => trim((string) ($_POST['nev']    ?? '')),
            'email'  => trim((string) ($_POST['email']  ?? '')),
            'targy'  => trim((string) ($_POST['targy']  ?? '')),
            'uzenet' => trim((string) ($_POST['uzenet'] ?? '')),
        ];

        $hibak = $this->validal($mezok);

        if (!empty($hibak)) {
            $this->nezet('kapcsolat/urlap', [
                'hibak' => $hibak,
                'mezok' => $mezok,
            ], 'Kapcsolat');
            return;
        }

        // Mentés DB-be
        $stmt = $this->dbh->prepare(
            'INSERT INTO uzenetek (user_id, nev, email, targy, uzenet)
             VALUES (:user_id, :nev, :email, :targy, :uzenet)'
        );
        $stmt->execute([
            ':user_id' => $_SESSION['felhasznalo_id'] ?? null,
            ':nev'     => $mezok['nev'],
            ':email'   => $mezok['email'],
            ':targy'   => $mezok['targy'],
            ':uzenet'  => $mezok['uzenet'],
        ]);

        // 5. oldal: a beküldött adatok visszajelzése
        $this->nezet('kapcsolat/siker', [
            'mezok'  => $mezok,
            'kuldve' => date('Y.m.d. H:i:s'),
        ], 'Üzenet elküldve');
    }

    /**
     * @param array<string, string> $mezok
     * @return array<string, string>
     */
    private function validal(array $mezok): array
    {
        $hibak = [];

        if ($mezok['nev'] === '' || mb_strlen($mezok['nev']) < 2) {
            $hibak['nev'] = 'A név megadása kötelező (legalább 2 karakter).';
        } elseif (mb_strlen($mezok['nev']) > 100) {
            $hibak['nev'] = 'A név legfeljebb 100 karakter lehet.';
        }

        if ($mezok['email'] === '') {
            $hibak['email'] = 'Az e-mail cím megadása kötelező.';
        } elseif (!filter_var($mezok['email'], FILTER_VALIDATE_EMAIL)) {
            $hibak['email'] = 'Érvényes e-mail címet adj meg.';
        }

        if ($mezok['targy'] === '' || mb_strlen($mezok['targy']) < 3) {
            $hibak['targy'] = 'A tárgy legalább 3 karakter legyen.';
        } elseif (mb_strlen($mezok['targy']) > 150) {
            $hibak['targy'] = 'A tárgy legfeljebb 150 karakter lehet.';
        }

        if ($mezok['uzenet'] === '' || mb_strlen($mezok['uzenet']) < 10) {
            $hibak['uzenet'] = 'Az üzenet legalább 10 karakteres legyen.';
        } elseif (mb_strlen($mezok['uzenet']) > 5000) {
            $hibak['uzenet'] = 'Az üzenet legfeljebb 5000 karakter lehet.';
        }

        return $hibak;
    }
}
