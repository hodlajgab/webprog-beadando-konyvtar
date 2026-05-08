<?php
/** @var string $uzenet */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
?>

<section class="hiba-oldal kozepre">
    <h1>404 — Nincs ilyen oldal</h1>
    <p class="hiba-uzenet"><?= $h($uzenet ?? 'A keresett oldal nem található.') ?></p>

    <p class="tavolsag-felul">
        <a class="gomb" href="/">Vissza a főoldalra</a>
        <a class="gomb gomb-masodlagos" href="/konyvek">Könyvek megtekintése</a>
    </p>
</section>

<style>
    .hiba-oldal {
        padding: 3rem 1rem;
    }
    .hiba-oldal h1 {
        font-size: 3rem;
        margin-bottom: 0.5rem;
    }
    .hiba-uzenet {
        font-size: 1.1rem;
        color: var(--szin-sotet);
    }
</style>
