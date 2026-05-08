<?php
/** @var array<int, array<string, mixed>> $konyvek */
/** @var string $kereses */
/** @var string $mufaj */
/** @var array<int, string> $mufajok */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
$szurve = ($kereses !== '') || ($mufaj !== '');
?>

<h1>Könyvek</h1>
<p>Az alkalmazásban nyilvántartott könyvek listája. A CRUD modul a 'konyvek' táblát kezeli.</p>

<form class="szuro-urlap" method="GET" action="/konyvek">
    <div class="szuro-mezo">
        <label for="kereses">Keresés</label>
        <input type="text" id="kereses" name="kereses"
               value="<?= $h($kereses) ?>"
               placeholder="Cím, szerző vagy kiadó…">
    </div>

    <div class="szuro-mezo">
        <label for="mufaj">Műfaj</label>
        <select id="mufaj" name="mufaj">
            <option value="">— mind —</option>
            <?php foreach ($mufajok as $m): ?>
                <option value="<?= $h($m) ?>" <?= $m === $mufaj ? 'selected' : '' ?>>
                    <?= $h($m) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="szuro-gombok">
        <button type="submit" class="gomb">Szűrés</button>
        <?php if ($szurve): ?>
            <a class="gomb gomb-masodlagos" href="/konyvek">Szűrő törlése</a>
        <?php endif; ?>
    </div>
</form>

<?php if (isset($_SESSION['felhasznalo_id'])): ?>
    <p><a class="gomb" href="/konyvek/uj">+ Új könyv hozzáadása</a></p>
<?php else: ?>
    <p class="info-szoveg">
        <em>Új könyv felvételéhez, szerkesztéshez vagy törléshez
        <a href="/belepes">jelentkezz be</a>.</em>
    </p>
<?php endif; ?>

<?php if ($szurve): ?>
    <p class="szuro-tajekoztato">
        <em>Szűrt találatok száma: <strong><?= count($konyvek) ?></strong></em>
    </p>
<?php endif; ?>

<style>
    .szuro-urlap {
        display: grid;
        gap: 0.75rem;
        background: white;
        padding: 1rem;
        border-radius: var(--kerekitett);
        box-shadow: var(--arnyek);
        margin-bottom: 1rem;
    }
    @media (min-width: 700px) {
        .szuro-urlap {
            grid-template-columns: 2fr 1fr auto;
            align-items: end;
        }
    }
    .szuro-mezo label {
        display: block;
        font-weight: bold;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    .szuro-mezo input,
    .szuro-mezo select {
        width: 100%;
        padding: 0.5rem;
        border: 1px solid var(--szin-keret);
        border-radius: var(--kerekitett);
        font-family: inherit;
        font-size: 1rem;
    }
    .szuro-gombok {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
</style>

<?php if (empty($konyvek)): ?>
    <p><em>Nincs rögzített könyv. Vegyél fel egy újat!</em></p>
<?php else: ?>
    <table class="tablazat">
        <thead>
            <tr>
                <th>Cím</th>
                <th>Szerző</th>
                <th>Kiadó</th>
                <th>Év</th>
                <th>Oldal</th>
                <th>Műfaj</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($konyvek as $k): ?>
                <tr>
                    <td data-cim="Cím"><strong><?= $h($k['cim']) ?></strong></td>
                    <td data-cim="Szerző"><?= $h($k['szerzo_nev'] ?? '—') ?></td>
                    <td data-cim="Kiadó"><?= $h($k['kiado_nev'] ?? '—') ?></td>
                    <td data-cim="Év"><?= $h((string) ($k['megjelenes_eve'] ?? '')) ?></td>
                    <td data-cim="Oldal"><?= $h((string) ($k['oldalszam'] ?? '')) ?></td>
                    <td data-cim="Műfaj"><?= $h($k['mufaj'] ?? '') ?></td>
                    <td data-cim="Műveletek">
                        <?php if (isset($_SESSION['felhasznalo_id'])): ?>
                            <div class="tablazat-cselekvesek">
                                <a class="gomb gomb-masodlagos"
                                   href="/konyvek/<?= (int) $k['id'] ?>/szerkesztes">Szerkesztés</a>
                                <form method="POST"
                                      action="/konyvek/<?= (int) $k['id'] ?>/torles"
                                      onsubmit="return confirm('Biztosan törlöd a következő könyvet: <?= $h($k['cim']) ?>?');"
                                      style="display:inline;">
                                    <input type="hidden" name="_csrf" value="<?= $h($csrf_token) ?>">
                                    <button type="submit" class="gomb gomb-veszelyes">Törlés</button>
                                </form>
                            </div>
                        <?php else: ?>
                            <em>—</em>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
