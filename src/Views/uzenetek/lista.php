<?php
/** @var array<int, array<string, mixed>> $uzenetek */
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
?>

<h1>Beérkezett üzenetek</h1>
<p>A kapcsolat-űrlapon érkezett üzenetek időrend szerint, a legfrissebbek felül.</p>

<?php if (empty($uzenetek)): ?>
    <p><em>Még nem érkezett üzenet.</em></p>
<?php else: ?>
    <table class="tablazat">
        <thead>
            <tr>
                <th>Küldés ideje</th>
                <th>Küldő</th>
                <th>E-mail</th>
                <th>Tárgy</th>
                <th>Üzenet</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($uzenetek as $u): ?>
                <?php
                    if ($u['user_id']) {
                        $kuldoNev = $u['csaladi_nev'] . ' ' . $u['uton_nev']
                                  . ' (' . $u['login'] . ')';
                    } else {
                        $kuldoNev = 'Vendég — ' . $u['nev'];
                    }
                ?>
                <tr>
                    <td data-cim="Küldés ideje"><?= $h(date('Y.m.d. H:i', strtotime((string) $u['kuldve']))) ?></td>
                    <td data-cim="Küldő"><?= $h($kuldoNev) ?></td>
                    <td data-cim="E-mail"><?= $h($u['email']) ?></td>
                    <td data-cim="Tárgy"><?= $h($u['targy']) ?></td>
                    <td data-cim="Üzenet"><?= nl2br($h($u['uzenet'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
