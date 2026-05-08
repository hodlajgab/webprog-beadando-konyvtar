<?php
/** @var array<int, array<string, mixed>> $kepek */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
?>

<h1>Képgaléria</h1>
<p>
    Itt találod a látogatók által feltöltött képeket. Saját kép feltöltéséhez
    <?php if (isset($_SESSION['felhasznalo_id'])): ?>
        kattints a gombra:
    <?php else: ?>
        <a href="/belepes">jelentkezz be</a>.
    <?php endif; ?>
</p>

<?php if (isset($_SESSION['felhasznalo_id'])): ?>
    <p><a class="gomb" href="/kepek/feltoltes">+ Új kép feltöltése</a></p>
<?php endif; ?>

<?php if (empty($kepek)): ?>
    <p><em>Még nincs feltöltött kép. Légy te az első!</em></p>
<?php else: ?>
    <div class="galeria">
        <?php foreach ($kepek as $k): ?>
            <figure>
                <img
                    src="/uploads/<?= $h($k['fajlnev']) ?>"
                    alt="<?= $h($k['leiras'] ?? $k['eredeti_nev']) ?>"
                    loading="lazy">
                <figcaption>
                    <strong><?= $h($k['eredeti_nev']) ?></strong>
                    <?php if (!empty($k['leiras'])): ?>
                        <br><?= $h($k['leiras']) ?>
                    <?php endif; ?>
                    <br>
                    <small>
                        <?= $h($k['csaladi_nev'] . ' ' . $k['uton_nev']) ?> ·
                        <?= $h(date('Y.m.d. H:i', strtotime((string) $k['feltoltve']))) ?>
                    </small>
                </figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
