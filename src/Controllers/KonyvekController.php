<?php
declare(strict_types=1);

namespace Controllers;

/**
 * CRUD modul a 'konyvek' táblához.
 *   - GET  /konyvek                          — lista
 *   - GET  /konyvek/uj                       — új könyv űrlap
 *   - POST /konyvek                          — új könyv mentése
 *   - GET  /konyvek/{id}/szerkesztes         — szerkesztő űrlap
 *   - POST /konyvek/{id}/szerkesztes         — frissítés mentése
 *   - POST /konyvek/{id}/torles              — törlés
 */
final class KonyvekController extends Controller
{
    public function lista(): void
    {
        $kereses = trim((string) ($_GET['kereses'] ?? ''));
        $mufaj   = trim((string) ($_GET['mufaj']   ?? ''));

        $sql = "SELECT k.id, k.cim, k.megjelenes_eve, k.oldalszam, k.mufaj,
                       CONCAT_WS(' ', sz.csaladi_nev, sz.uton_nev) AS szerzo_nev,
                       ki.nev AS kiado_nev
                FROM konyvek k
                LEFT JOIN szerzok sz ON sz.id = k.szerzo_id
                LEFT JOIN kiadok  ki ON ki.id = k.kiado_id
                WHERE 1 = 1";

        $parameterek = [];

        if ($kereses !== '') {
            $sql .= " AND (k.cim LIKE :kereses
                       OR sz.csaladi_nev LIKE :kereses
                       OR sz.uton_nev LIKE :kereses
                       OR ki.nev LIKE :kereses)";
            $parameterek[':kereses'] = '%' . $kereses . '%';
        }

        if ($mufaj !== '') {
            $sql .= " AND k.mufaj = :mufaj";
            $parameterek[':mufaj'] = $mufaj;
        }

        $sql .= " ORDER BY k.cim ASC";

        $stmt = $this->dbh->prepare($sql);
        $stmt->execute($parameterek);
        $konyvek = $stmt->fetchAll();

        // Egyedi műfajok a szűrő legördülő listához
        $mufajok = $this->dbh->query(
            "SELECT DISTINCT mufaj FROM konyvek WHERE mufaj IS NOT NULL AND mufaj <> '' ORDER BY mufaj"
        )->fetchAll(\PDO::FETCH_COLUMN);

        $this->nezet('konyvek/lista', [
            'konyvek'  => $konyvek,
            'kereses'  => $kereses,
            'mufaj'    => $mufaj,
            'mufajok'  => $mufajok,
        ], 'Könyvek');
    }

    public function ujUrlap(): void
    {
        $this->csakBelepve();
        $this->nezet('konyvek/urlap', [
            'konyv'    => $this->uresKonyv(),
            'szerzok'  => $this->szerzokListaja(),
            'kiadok'   => $this->kiadokListaja(),
            'hibak'    => [],
            'cselekves' => '/konyvek',
            'cim'       => 'Új könyv',
        ], 'Új könyv');
    }

    public function letrehoz(): void
    {
        $this->csakBelepve();
        $adatok = $this->postAdatok();
        $hibak  = $this->validal($adatok);

        if (!empty($hibak)) {
            $this->nezet('konyvek/urlap', [
                'konyv'     => $adatok,
                'szerzok'   => $this->szerzokListaja(),
                'kiadok'    => $this->kiadokListaja(),
                'hibak'     => $hibak,
                'cselekves' => '/konyvek',
                'cim'       => 'Új könyv',
            ], 'Új könyv');
            return;
        }

        $stmt = $this->dbh->prepare(
            'INSERT INTO konyvek (cim, szerzo_id, kiado_id, megjelenes_eve, oldalszam, mufaj, leiras)
             VALUES (:cim, :szid, :kid, :ev, :ol, :mu, :le)'
        );
        $stmt->execute([
            ':cim'  => $adatok['cim'],
            ':szid' => $adatok['szerzo_id'] ?: null,
            ':kid'  => $adatok['kiado_id']  ?: null,
            ':ev'   => $adatok['megjelenes_eve'] !== '' ? (int) $adatok['megjelenes_eve'] : null,
            ':ol'   => $adatok['oldalszam']      !== '' ? (int) $adatok['oldalszam']      : null,
            ':mu'   => $adatok['mufaj'] !== '' ? $adatok['mufaj'] : null,
            ':le'   => $adatok['leiras'] !== '' ? $adatok['leiras'] : null,
        ]);

        $this->flash('siker', 'Könyv sikeresen rögzítve.');
        $this->atiranyit('/konyvek');
    }

    /**
     * @param array{id: string} $parameterek
     */
    public function szerkesztesUrlap(array $parameterek): void
    {
        $this->csakBelepve();
        $id = (int) ($parameterek['id'] ?? 0);
        $konyv = $this->konyvBetolt($id);
        if ($konyv === null) {
            $this->flash('hiba', 'A keresett könyv nem található.');
            $this->atiranyit('/konyvek');
            return;
        }

        $this->nezet('konyvek/urlap', [
            'konyv'     => $konyv,
            'szerzok'   => $this->szerzokListaja(),
            'kiadok'    => $this->kiadokListaja(),
            'hibak'     => [],
            'cselekves' => '/konyvek/' . $id . '/szerkesztes',
            'cim'       => 'Könyv szerkesztése',
        ], 'Könyv szerkesztése');
    }

    /**
     * @param array{id: string} $parameterek
     */
    public function frissit(array $parameterek): void
    {
        $this->csakBelepve();
        $id = (int) ($parameterek['id'] ?? 0);
        if ($this->konyvBetolt($id) === null) {
            $this->flash('hiba', 'A frissítendő könyv nem található.');
            $this->atiranyit('/konyvek');
            return;
        }

        $adatok = $this->postAdatok();
        $hibak  = $this->validal($adatok);

        if (!empty($hibak)) {
            $adatok['id'] = $id;
            $this->nezet('konyvek/urlap', [
                'konyv'     => $adatok,
                'szerzok'   => $this->szerzokListaja(),
                'kiadok'    => $this->kiadokListaja(),
                'hibak'     => $hibak,
                'cselekves' => '/konyvek/' . $id . '/szerkesztes',
                'cim'       => 'Könyv szerkesztése',
            ], 'Könyv szerkesztése');
            return;
        }

        $stmt = $this->dbh->prepare(
            'UPDATE konyvek
             SET cim = :cim, szerzo_id = :szid, kiado_id = :kid,
                 megjelenes_eve = :ev, oldalszam = :ol, mufaj = :mu, leiras = :le
             WHERE id = :id'
        );
        $stmt->execute([
            ':cim'  => $adatok['cim'],
            ':szid' => $adatok['szerzo_id'] ?: null,
            ':kid'  => $adatok['kiado_id']  ?: null,
            ':ev'   => $adatok['megjelenes_eve'] !== '' ? (int) $adatok['megjelenes_eve'] : null,
            ':ol'   => $adatok['oldalszam']      !== '' ? (int) $adatok['oldalszam']      : null,
            ':mu'   => $adatok['mufaj'] !== '' ? $adatok['mufaj'] : null,
            ':le'   => $adatok['leiras'] !== '' ? $adatok['leiras'] : null,
            ':id'   => $id,
        ]);

        $this->flash('siker', 'A könyv adatai frissítve.');
        $this->atiranyit('/konyvek');
    }

    /**
     * @param array{id: string} $parameterek
     */
    public function torles(array $parameterek): void
    {
        $this->csakBelepve();
        $id = (int) ($parameterek['id'] ?? 0);
        $stmt = $this->dbh->prepare('DELETE FROM konyvek WHERE id = :id');
        $stmt->execute([':id' => $id]);

        if ($stmt->rowCount() > 0) {
            $this->flash('siker', 'Könyv törölve.');
        } else {
            $this->flash('hiba', 'A törlendő könyv nem található.');
        }

        $this->atiranyit('/konyvek');
    }

    // ----- Segéd-metódusok -----

    /**
     * @return array<string, mixed>
     */
    private function postAdatok(): array
    {
        return [
            'cim'             => trim((string) ($_POST['cim']             ?? '')),
            'szerzo_id'       => (string) ($_POST['szerzo_id']       ?? ''),
            'kiado_id'        => (string) ($_POST['kiado_id']        ?? ''),
            'megjelenes_eve'  => trim((string) ($_POST['megjelenes_eve'] ?? '')),
            'oldalszam'       => trim((string) ($_POST['oldalszam']      ?? '')),
            'mufaj'           => trim((string) ($_POST['mufaj']           ?? '')),
            'leiras'          => trim((string) ($_POST['leiras']          ?? '')),
        ];
    }

    /**
     * @param array<string, mixed> $a
     * @return array<string, string>
     */
    private function validal(array $a): array
    {
        $hibak = [];
        $cim = (string) $a['cim'];

        if ($cim === '' || mb_strlen($cim) < 2) {
            $hibak['cim'] = 'A cím megadása kötelező (legalább 2 karakter).';
        } elseif (mb_strlen($cim) > 200) {
            $hibak['cim'] = 'A cím legfeljebb 200 karakter lehet.';
        }

        if ($a['megjelenes_eve'] !== '') {
            $ev = (int) $a['megjelenes_eve'];
            if ($ev < 1000 || $ev > (int) date('Y') + 1) {
                $hibak['megjelenes_eve'] = 'Adj meg érvényes évszámot (1000 és ' . (date('Y') + 1) . ' között).';
            }
        }

        if ($a['oldalszam'] !== '') {
            $ol = (int) $a['oldalszam'];
            if ($ol <= 0 || $ol > 100000) {
                $hibak['oldalszam'] = 'Az oldalszámnak pozitív, ésszerű számnak kell lennie.';
            }
        }

        if (mb_strlen((string) $a['mufaj']) > 60) {
            $hibak['mufaj'] = 'A műfaj legfeljebb 60 karakter lehet.';
        }

        return $hibak;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function konyvBetolt(int $id): ?array
    {
        $stmt = $this->dbh->prepare('SELECT * FROM konyvek WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $sor = $stmt->fetch();
        return $sor === false ? null : $sor;
    }

    /** @return array<int, array<string, mixed>> */
    private function szerzokListaja(): array
    {
        return $this->dbh->query(
            "SELECT id, CONCAT_WS(' ', csaladi_nev, uton_nev) AS nev
             FROM szerzok ORDER BY csaladi_nev, uton_nev"
        )->fetchAll();
    }

    /** @return array<int, array<string, mixed>> */
    private function kiadokListaja(): array
    {
        return $this->dbh->query('SELECT id, nev FROM kiadok ORDER BY nev')->fetchAll();
    }

    /** @return array<string, mixed> */
    private function uresKonyv(): array
    {
        return [
            'id' => 0,
            'cim' => '',
            'szerzo_id' => '',
            'kiado_id' => '',
            'megjelenes_eve' => '',
            'oldalszam' => '',
            'mufaj' => '',
            'leiras' => '',
        ];
    }
}
