<?php
/** @var array<string,string> $belepes_hibak */
/** @var array<string,string> $regisztracio_hibak */
/** @var string $regi_login */
/** @var string $regi_email */
/** @var string $regi_csaladi_nev */
/** @var string $regi_uton_nev */

$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
$reg_login_regi = $regi_reg_login ?? '';
?>

<h1>Bejelentkezés / Regisztráció</h1>

<?php if (!empty($belepes_hibak['altalanos'])): ?>
    <div class="uzenet uzenet-hiba"><?= $h($belepes_hibak['altalanos']) ?></div>
<?php endif; ?>

<div class="auth-paneek">

    <section class="urlap">
        <h2>Bejelentkezés</h2>
        <form method="POST" action="/belepes" novalidate>
            <div class="urlap-mezo">
                <label for="login">Felhasználónév</label>
                <input type="text" id="login" name="login" value="<?= $h($regi_login) ?>">
                <div class="urlap-hibauzenet"><?= $h($belepes_hibak['login'] ?? '') ?></div>
            </div>

            <div class="urlap-mezo">
                <label for="jelszo">Jelszó</label>
                <input type="password" id="jelszo" name="jelszo">
                <div class="urlap-hibauzenet"><?= $h($belepes_hibak['jelszo'] ?? '') ?></div>
            </div>

            <button type="submit" class="gomb">Belépés</button>
        </form>
    </section>

    <section class="urlap">
        <h2>Regisztráció</h2>
        <form method="POST" action="/regisztracio" novalidate>
            <div class="urlap-mezo">
                <label for="reg_login">Felhasználónév</label>
                <input type="text" id="reg_login" name="reg_login" value="<?= $h($reg_login_regi) ?>">
                <div class="urlap-hibauzenet"><?= $h($regisztracio_hibak['reg_login'] ?? '') ?></div>
            </div>

            <div class="urlap-mezo">
                <label for="reg_email">E-mail cím</label>
                <input type="text" id="reg_email" name="reg_email" value="<?= $h($regi_email) ?>">
                <div class="urlap-hibauzenet"><?= $h($regisztracio_hibak['reg_email'] ?? '') ?></div>
            </div>

            <div class="urlap-mezo">
                <label for="reg_csaladi_nev">Családi név</label>
                <input type="text" id="reg_csaladi_nev" name="reg_csaladi_nev" value="<?= $h($regi_csaladi_nev) ?>">
                <div class="urlap-hibauzenet"><?= $h($regisztracio_hibak['reg_csaladi_nev'] ?? '') ?></div>
            </div>

            <div class="urlap-mezo">
                <label for="reg_uton_nev">Utónév</label>
                <input type="text" id="reg_uton_nev" name="reg_uton_nev" value="<?= $h($regi_uton_nev) ?>">
                <div class="urlap-hibauzenet"><?= $h($regisztracio_hibak['reg_uton_nev'] ?? '') ?></div>
            </div>

            <div class="urlap-mezo">
                <label for="reg_jelszo">Jelszó (legalább 8 karakter)</label>
                <input type="password" id="reg_jelszo" name="reg_jelszo">
                <div class="urlap-hibauzenet"><?= $h($regisztracio_hibak['reg_jelszo'] ?? '') ?></div>
            </div>

            <div class="urlap-mezo">
                <label for="reg_jelszo_megegyszer">Jelszó még egyszer</label>
                <input type="password" id="reg_jelszo_megegyszer" name="reg_jelszo_megegyszer">
                <div class="urlap-hibauzenet"><?= $h($regisztracio_hibak['reg_jelszo_megegyszer'] ?? '') ?></div>
            </div>

            <button type="submit" class="gomb">Regisztráció</button>
        </form>
    </section>

</div>

<style>
    .auth-paneek {
        display: grid;
        gap: 2rem;
        margin-top: 2rem;
    }
    @media (min-width: 768px) {
        .auth-paneek {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>
