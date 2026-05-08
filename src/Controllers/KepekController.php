<?php
declare(strict_types=1);

namespace Controllers;

/**
 * Képgaléria + biztonságos képfeltöltés.
 */
final class KepekController extends Controller
{
    /** @var array<int, string> engedélyezett MIME → kiterjesztés */
    private const ENGEDELYEZETT_MIME = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    private const MAX_MERET_BAJT = 5 * 1024 * 1024; // 5 MB

    public function lista(): void
    {
        $stmt = $this->dbh->query(
            'SELECT k.id, k.fajlnev, k.eredeti_nev, k.leiras, k.feltoltve,
                    u.csaladi_nev, u.uton_nev, u.login
             FROM kepek k
             JOIN users u ON u.id = k.feltolto_id
             ORDER BY k.feltoltve DESC'
        );
        $kepek = $stmt->fetchAll();

        $this->nezet('kepek/lista', ['kepek' => $kepek], 'Képek — Galéria');
    }

    public function feltoltesUrlap(): void
    {
        $this->csakBelepve();
        $this->nezet('kepek/feltoltes', ['hibak' => [], 'regi_leiras' => ''], 'Kép feltöltése');
    }

    public function feltoltes(): void
    {
        $this->csrfEllenoriz();
        $this->csakBelepve();

        $hibak  = [];
        $leiras = trim((string) ($_POST['leiras'] ?? ''));

        if (!isset($_FILES['kep']) || !is_array($_FILES['kep']) || $_FILES['kep']['error'] === UPLOAD_ERR_NO_FILE) {
            $hibak['kep'] = 'Válassz ki egy fájlt a feltöltéshez.';
        } elseif ($_FILES['kep']['error'] !== UPLOAD_ERR_OK) {
            $hibak['kep'] = 'Hiba történt a feltöltés során (kód: ' . (int) $_FILES['kep']['error'] . ').';
        }

        if (empty($hibak)) {
            $merete = (int) $_FILES['kep']['size'];
            if ($merete <= 0) {
                $hibak['kep'] = 'A fájl üres.';
            } elseif ($merete > self::MAX_MERET_BAJT) {
                $hibak['kep'] = 'A fájl mérete nem lehet több, mint 5 MB.';
            }
        }

        if (empty($hibak)) {
            // MIME-típus a fájl tartalmából, NEM a $_FILES['type']-ból (azt a kliens állítja)
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($_FILES['kep']['tmp_name']);

            if (!isset(self::ENGEDELYEZETT_MIME[$mime])) {
                $hibak['kep'] = 'Csak JPG, PNG, GIF vagy WEBP képeket lehet feltölteni.';
            }
        }

        if (mb_strlen($leiras) > 255) {
            $hibak['leiras'] = 'A leírás legfeljebb 255 karakter lehet.';
        }

        if (!empty($hibak)) {
            $this->nezet('kepek/feltoltes', ['hibak' => $hibak, 'regi_leiras' => $leiras], 'Kép feltöltése');
            return;
        }

        // Generált fájlnév — soha ne a user által megadottat használjuk
        $kiterjesztes = self::ENGEDELYEZETT_MIME[$mime];
        $generaltNev  = bin2hex(random_bytes(16)) . '.' . $kiterjesztes;
        $celUtvonal   = __DIR__ . '/../../public/uploads/' . $generaltNev;
        $eredetiNev   = (string) ($_FILES['kep']['name'] ?? 'ismeretlen');

        if (!move_uploaded_file($_FILES['kep']['tmp_name'], $celUtvonal)) {
            $this->flash('hiba', 'A fájl mentése nem sikerült. Próbáld újra.');
            $this->atiranyit('/kepek/feltoltes');
            return;
        }

        $stmt = $this->dbh->prepare(
            'INSERT INTO kepek (feltolto_id, fajlnev, eredeti_nev, leiras)
             VALUES (:fid, :fnev, :enev, :leiras)'
        );
        $stmt->execute([
            ':fid'    => $_SESSION['felhasznalo_id'],
            ':fnev'   => $generaltNev,
            ':enev'   => mb_substr($eredetiNev, 0, 255),
            ':leiras' => $leiras !== '' ? $leiras : null,
        ]);

        $this->flash('siker', 'A kép sikeresen feltöltve!');
        $this->atiranyit('/kepek');
    }
}
