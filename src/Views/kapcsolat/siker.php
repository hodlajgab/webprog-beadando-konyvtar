<?php
/** @var array<string,string> $mezok */
/** @var string $kuldve */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
?>

<h1>Köszönjük az üzenetet!</h1>

<div class="uzenet uzenet-siker">
    <strong>Az üzenet elküldve.</strong>
    Hamarosan válaszolunk az alábbi e-mail címre.
</div>

<section class="urlap">
    <h2>A beküldött adatok</h2>
    <dl>
        <dt><strong>Küldés ideje:</strong></dt>
        <dd><?= $h($kuldve) ?></dd>

        <dt><strong>Név:</strong></dt>
        <dd><?= $h($mezok['nev']) ?></dd>

        <dt><strong>E-mail cím:</strong></dt>
        <dd><?= $h($mezok['email']) ?></dd>

        <dt><strong>Tárgy:</strong></dt>
        <dd><?= $h($mezok['targy']) ?></dd>

        <dt><strong>Üzenet:</strong></dt>
        <dd><?= nl2br($h($mezok['uzenet'])) ?></dd>
    </dl>
</section>

<p class="tavolsag-felul">
    <a class="gomb" href="/">Vissza a főoldalra</a>
    <a class="gomb gomb-masodlagos" href="/kapcsolat">Új üzenet írása</a>
</p>

<style>
    dl dt { margin-top: 0.75rem; }
    dl dd { margin-left: 0; margin-bottom: 0.5rem; }
</style>
