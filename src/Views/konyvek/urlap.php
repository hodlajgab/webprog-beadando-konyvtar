<?php
/** @var array<string, mixed> $konyv */
/** @var array<int, array<string, mixed>> $szerzok */
/** @var array<int, array<string, mixed>> $kiadok */
/** @var array<string, string> $hibak */
/** @var string $cselekves */
/** @var string $cim */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
$ujE = ((int) ($konyv['id'] ?? 0)) === 0;
?>

<h1><?= $h($cim) ?></h1>

<form class="urlap" method="POST" action="<?= $h($cselekves) ?>" novalidate>
    <input type="hidden" name="_csrf" value="<?= $h($csrf_token) ?>">
    <div class="urlap-mezo">
        <label for="cim">Cím *</label>
        <input type="text" id="cim" name="cim" value="<?= $h((string) $konyv['cim']) ?>">
        <div class="urlap-hibauzenet"><?= $h($hibak['cim'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="szerzo_id">Szerző</label>
        <select id="szerzo_id" name="szerzo_id">
            <option value="">— válassz —</option>
            <?php foreach ($szerzok as $sz): ?>
                <option value="<?= (int) $sz['id'] ?>"
                    <?= ((string) $sz['id'] === (string) $konyv['szerzo_id']) ? 'selected' : '' ?>>
                    <?= $h($sz['nev']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="urlap-mezo">
        <label for="kiado_id">Kiadó</label>
        <select id="kiado_id" name="kiado_id">
            <option value="">— válassz —</option>
            <?php foreach ($kiadok as $ki): ?>
                <option value="<?= (int) $ki['id'] ?>"
                    <?= ((string) $ki['id'] === (string) $konyv['kiado_id']) ? 'selected' : '' ?>>
                    <?= $h($ki['nev']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="urlap-mezo">
        <label for="megjelenes_eve">Megjelenés éve</label>
        <input type="text" id="megjelenes_eve" name="megjelenes_eve"
               value="<?= $h((string) $konyv['megjelenes_eve']) ?>"
               inputmode="numeric">
        <div class="urlap-hibauzenet"><?= $h($hibak['megjelenes_eve'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="oldalszam">Oldalszám</label>
        <input type="text" id="oldalszam" name="oldalszam"
               value="<?= $h((string) $konyv['oldalszam']) ?>"
               inputmode="numeric">
        <div class="urlap-hibauzenet"><?= $h($hibak['oldalszam'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="mufaj">Műfaj</label>
        <input type="text" id="mufaj" name="mufaj" value="<?= $h((string) $konyv['mufaj']) ?>">
        <div class="urlap-hibauzenet"><?= $h($hibak['mufaj'] ?? '') ?></div>
    </div>

    <div class="urlap-mezo">
        <label for="leiras">Leírás</label>
        <textarea id="leiras" name="leiras"><?= $h((string) $konyv['leiras']) ?></textarea>
    </div>

    <button type="submit" class="gomb">
        <?= $ujE ? 'Könyv mentése' : 'Módosítások mentése' ?>
    </button>
    <a class="gomb gomb-masodlagos" href="/konyvek">Mégse</a>
</form>
