<?php
/** @var string $cim */
/** @var string $tartalom */
$cim = $cim ?? 'Könyvtár — Web-programozás 1 beadandó';
$tartalom = $tartalom ?? '';
$bejelentkezett = isset($_SESSION['felhasznalo_id']);
$csaladi = $_SESSION['felhasznalo_csaladi_nev'] ?? '';
$uton    = $_SESSION['felhasznalo_uton_nev']    ?? '';
$login   = $_SESSION['felhasznalo_login']       ?? '';

$flashek = $_SESSION['flash'] ?? [];
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($cim, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/css/stilus.css">
</head>
<body>
    <header class="oldal-fejlec">
        <div class="fejlec-tartalom">
            <a class="logo" href="/">📚 Könyvtár</a>

            <?php if ($bejelentkezett): ?>
                <span class="bejelentkezett-info">
                    Bejelentkezett:
                    <strong>
                        <?= htmlspecialchars($csaladi . ' ' . $uton, ENT_QUOTES, 'UTF-8') ?>
                    </strong>
                    (<?= htmlspecialchars($login, ENT_QUOTES, 'UTF-8') ?>)
                </span>
            <?php endif; ?>

            <button class="hamburger" aria-label="Menü" aria-expanded="false" aria-controls="fomenu">
                <span></span><span></span><span></span>
            </button>
        </div>

        <nav id="fomenu" class="menu-vizszintes" aria-label="Főmenü">
            <ul>
                <li><a href="/">Főoldal</a></li>
                <li><a href="/kepek">Képek</a></li>
                <li><a href="/kapcsolat">Kapcsolat</a></li>
                <li><a href="/konyvek">CRUD</a></li>
                <?php if ($bejelentkezett): ?>
                    <li><a href="/uzenetek">Üzenetek</a></li>
                    <li>
                        <form method="POST" action="/kilepes" class="kilepes-urlap">
                            <input type="hidden" name="_csrf"
                                   value="<?= htmlspecialchars($csrf_token ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="kilepes-gomb">Kilépés</button>
                        </form>
                    </li>
                <?php else: ?>
                    <li><a href="/belepes">Bejelentkezés</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="oldal-tartalom">
        <?php if (!empty($flashek)): ?>
            <div class="uzenetek">
                <?php foreach ($flashek as $f): ?>
                    <div class="uzenet uzenet-<?= htmlspecialchars($f['tipus'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($f['uzenet'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?= $tartalom ?>
    </main>

    <footer class="oldal-labrec">
        <p>&copy; <?= date('Y') ?> Könyvtár webalkalmazás · Web-programozás 1 beadandó</p>
    </footer>

    <script src="/assets/js/menu.js"></script>
</body>
</html>
