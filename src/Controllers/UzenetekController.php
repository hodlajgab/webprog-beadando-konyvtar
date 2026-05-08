<?php
declare(strict_types=1);

namespace Controllers;

/**
 * Üzenetek menü — csak bejelentkezett user láthatja.
 * Az uzenetek táblát fordított időrendben (legfrissebb felül) jeleníti meg.
 */
final class UzenetekController extends Controller
{
    public function lista(): void
    {
        $this->csakBelepve();

        $stmt = $this->dbh->query(
            'SELECT u.id, u.nev, u.email, u.targy, u.uzenet, u.kuldve, u.user_id,
                    f.csaladi_nev, f.uton_nev, f.login
             FROM uzenetek u
             LEFT JOIN users f ON f.id = u.user_id
             ORDER BY u.kuldve DESC'
        );
        $uzenetek = $stmt->fetchAll();

        $this->nezet('uzenetek/lista', ['uzenetek' => $uzenetek], 'Üzenetek');
    }
}
