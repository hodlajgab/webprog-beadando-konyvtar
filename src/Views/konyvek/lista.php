<?php
/** @var array<int, array<string, mixed>> $konyvek */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
?>

<h1>Könyvek</h1>
<p>Az alkalmazásban nyilvántartott könyvek listája. A CRUD modul a 'konyvek' táblát kezeli.</p>

<p><a class="gomb" href="/konyvek/uj">+ Új könyv hozzáadása</a></p>

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
                        <div class="tablazat-cselekvesek">
                            <a class="gomb gomb-masodlagos"
                               href="/konyvek/<?= (int) $k['id'] ?>/szerkesztes">Szerkesztés</a>
                            <form method="POST"
                                  action="/konyvek/<?= (int) $k['id'] ?>/torles"
                                  onsubmit="return confirm('Biztosan törlöd a következő könyvet: <?= $h($k['cim']) ?>?');"
                                  style="display:inline;">
                                <button type="submit" class="gomb gomb-veszelyes">Törlés</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
