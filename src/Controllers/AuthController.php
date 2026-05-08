<?php
declare(strict_types=1);

namespace Controllers;

/**
 * Bejelentkezés, regisztráció, kilépés.
 *
 * A feladatkiírás szerint:
 *   - "Belépés" menüpontra kattintva egy oldalon legyen a bejelentkezés és a regisztráció is.
 *   - Regisztrációkor NE léptessük be automatikusan a felhasználót.
 *   - Kilépés után visszaköltöztetjük a főoldalra.
 */
final class AuthController extends Controller
{
    public function belepesUrlap(): void
    {
        if ($this->bejelentkezett()) {
            $this->atiranyit('/');
            return;
        }

        $this->nezet('auth/belepes', [
            'belepes_hibak'      => [],
            'regisztracio_hibak' => [],
            'regi_login'         => '',
            'regi_email'         => '',
            'regi_csaladi_nev'   => '',
            'regi_uton_nev'      => '',
        ], 'Bejelentkezés / Regisztráció');
    }

    public function belepes(): void
    {
        $this->csrfEllenoriz();

        if ($this->bejelentkezett()) {
            $this->atiranyit('/');
            return;
        }

        $login  = trim((string) ($_POST['login']  ?? ''));
        $jelszo = (string) ($_POST['jelszo'] ?? '');
        $hibak  = [];

        if ($login === '') {
            $hibak['login'] = 'A felhasználónév megadása kötelező.';
        }
        if ($jelszo === '') {
            $hibak['jelszo'] = 'A jelszó megadása kötelező.';
        }

        if (empty($hibak)) {
            $stmt = $this->dbh->prepare(
                'SELECT id, login, jelszo_hash, csaladi_nev, uton_nev FROM users WHERE login = :login LIMIT 1'
            );
            $stmt->execute([':login' => $login]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($jelszo, $user['jelszo_hash'])) {
                $hibak['altalanos'] = 'Hibás felhasználónév vagy jelszó.';
            } else {
                // Sikeres belépés — session fixation védelem
                session_regenerate_id(true);
                $_SESSION['felhasznalo_id']          = (int) $user['id'];
                $_SESSION['felhasznalo_login']       = $user['login'];
                $_SESSION['felhasznalo_csaladi_nev'] = $user['csaladi_nev'];
                $_SESSION['felhasznalo_uton_nev']    = $user['uton_nev'];

                $this->flash('siker', 'Sikeres bejelentkezés. Üdv, ' . $user['csaladi_nev'] . ' ' . $user['uton_nev'] . '!');
                $this->atiranyit('/');
                return;
            }
        }

        $this->nezet('auth/belepes', [
            'belepes_hibak'      => $hibak,
            'regisztracio_hibak' => [],
            'regi_login'         => $login,
            'regi_email'         => '',
            'regi_csaladi_nev'   => '',
            'regi_uton_nev'      => '',
        ], 'Bejelentkezés / Regisztráció');
    }

    public function regisztracio(): void
    {
        $this->csrfEllenoriz();

        if ($this->bejelentkezett()) {
            $this->atiranyit('/');
            return;
        }

        $login       = trim((string) ($_POST['reg_login']       ?? ''));
        $email       = trim((string) ($_POST['reg_email']       ?? ''));
        $csaladi     = trim((string) ($_POST['reg_csaladi_nev'] ?? ''));
        $uton        = trim((string) ($_POST['reg_uton_nev']    ?? ''));
        $jelszo      = (string)        ($_POST['reg_jelszo']      ?? '');
        $jelszo2     = (string)        ($_POST['reg_jelszo_megegyszer'] ?? '');

        $hibak = [];

        if ($login === '' || mb_strlen($login) < 3) {
            $hibak['reg_login'] = 'A felhasználónév legalább 3 karakter legyen.';
        } elseif (!preg_match('/^[A-Za-z0-9._-]+$/', $login)) {
            $hibak['reg_login'] = 'A felhasználónév csak betűt, számot, ponton, kötőjelet, aláhúzást tartalmazhat.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $hibak['reg_email'] = 'Érvényes e-mail címet adj meg.';
        }

        if ($csaladi === '') {
            $hibak['reg_csaladi_nev'] = 'A családi név megadása kötelező.';
        }
        if ($uton === '') {
            $hibak['reg_uton_nev'] = 'Az utónév megadása kötelező.';
        }

        if (mb_strlen($jelszo) < 8) {
            $hibak['reg_jelszo'] = 'A jelszó legalább 8 karakter legyen.';
        }
        if ($jelszo !== $jelszo2) {
            $hibak['reg_jelszo_megegyszer'] = 'A két jelszó nem egyezik.';
        }

        // Foglalt-e a login / email
        if (!isset($hibak['reg_login'])) {
            $stmt = $this->dbh->prepare('SELECT id FROM users WHERE login = :login LIMIT 1');
            $stmt->execute([':login' => $login]);
            if ($stmt->fetch()) {
                $hibak['reg_login'] = 'Ez a felhasználónév már foglalt.';
            }
        }
        if (!isset($hibak['reg_email'])) {
            $stmt = $this->dbh->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $hibak['reg_email'] = 'Ezzel az e-mail címmel már regisztráltak.';
            }
        }

        if (!empty($hibak)) {
            $this->nezet('auth/belepes', [
                'belepes_hibak'      => [],
                'regisztracio_hibak' => $hibak,
                'regi_login'         => '',
                'regi_email'         => $email,
                'regi_csaladi_nev'   => $csaladi,
                'regi_uton_nev'      => $uton,
                'regi_reg_login'     => $login,
            ], 'Bejelentkezés / Regisztráció');
            return;
        }

        // Mentés
        $stmt = $this->dbh->prepare(
            'INSERT INTO users (login, jelszo_hash, csaladi_nev, uton_nev, email)
             VALUES (:login, :jelszo_hash, :csaladi, :uton, :email)'
        );
        $stmt->execute([
            ':login'       => $login,
            ':jelszo_hash' => password_hash($jelszo, PASSWORD_DEFAULT),
            ':csaladi'     => $csaladi,
            ':uton'        => $uton,
            ':email'       => $email,
        ]);

        // FONTOS: regisztráció után NEM léptetjük be automatikusan — a feladatkiírás kötelezi
        $this->flash('siker', 'Sikeres regisztráció! Most már bejelentkezhetsz.');
        $this->atiranyit('/belepes');
    }

    public function kilepes(): void
    {
        $this->csrfEllenoriz();

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        session_start();

        $this->flash('info', 'Sikeresen kijelentkeztél.');
        $this->atiranyit('/');
    }
}
