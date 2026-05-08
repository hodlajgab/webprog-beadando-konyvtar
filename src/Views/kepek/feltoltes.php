<?php
/** @var array<string,string> $hibak */
/** @var string $regi_leiras */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
?>

<h1>Kép feltöltése</h1>

<form class="urlap" method="POST" action="/kepek/feltoltes" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="_csrf" value="<?= $h($csrf_token) ?>">
    <div class="urlap-mezo">
        <label for="kep">Kép fájl (JPG, PNG, GIF, WEBP — max 5 MB)</label>
        <input type="file" id="kep" name="kep" accept="image/jpeg,image/png,image/gif,image/webp">
        <div class="urlap-hibauzenet"><?= $h($hibak['kep'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="leiras">Leírás (opcionális, max 255 karakter)</label>
        <input type="text" id="leiras" name="leiras" value="<?= $h($regi_leiras) ?>">
        <div class="urlap-hibauzenet"><?= $h($hibak['leiras'] ?? '') ?></div>
    </div>

    <button type="submit" class="gomb">Feltöltés</button>
    <a href="/kepek" class="gomb gomb-masodlagos">Mégse</a>
</form>
