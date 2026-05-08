<?php
/** @var array<string,string> $hibak */
/** @var array<string,string> $mezok */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
?>

<h1>Kapcsolat</h1>
<p>Ha bármilyen kérdésed, észrevételed van, küldj nekünk üzenetet az alábbi űrlapon!</p>

<form id="kapcsolatUrlap" class="urlap" method="POST" action="/kapcsolat" novalidate>
    <div class="urlap-mezo">
        <label for="nev">Név</label>
        <input type="text" id="nev" name="nev" value="<?= $h($mezok['nev']) ?>">
        <div class="urlap-hibauzenet" id="hiba_nev"><?= $h($hibak['nev'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="email">E-mail cím</label>
        <input type="text" id="email" name="email" value="<?= $h($mezok['email']) ?>">
        <div class="urlap-hibauzenet" id="hiba_email"><?= $h($hibak['email'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="targy">Tárgy</label>
        <input type="text" id="targy" name="targy" value="<?= $h($mezok['targy']) ?>">
        <div class="urlap-hibauzenet" id="hiba_targy"><?= $h($hibak['targy'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="uzenet">Üzenet</label>
        <textarea id="uzenet" name="uzenet"><?= $h($mezok['uzenet']) ?></textarea>
        <div class="urlap-hibauzenet" id="hiba_uzenet"><?= $h($hibak['uzenet'] ?? '') ?></div>
    </div>

    <button type="submit" class="gomb">Üzenet elküldése</button>
</form>

<script src="/assets/js/kapcsolat.js"></script>
