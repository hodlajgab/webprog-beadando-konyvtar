# Web-programozás 1 — Beadandó dokumentáció

> Ez a fájl a beadandó **15+ oldalas PDF dokumentációjának vázlata**.
> A `[KITÖLTENDŐ: ...]` placeholder-eket pótold a véglegesítés előtt.
> A `📷` jelölésű helyekre ne felejts el képernyőképet illeszteni.

---

## Címlap

**Projekt címe:** Könyvtár — webalkalmazás könyvek nyilvántartására
**Tantárgy:** Web-programozás 1 — Gyakorlat
**Készítette:**
- `[KITÖLTENDŐ: Hallgató 1 neve – Neptun-kód]`
- `[KITÖLTENDŐ: Hallgató 2 neve – Neptun-kód]` *(töröld, ha egyedül készült)*

**Választott adatbázis-forrás:** Könyvtár (Drive-ról)
**Dátum:** `[KITÖLTENDŐ: 2026-MM-DD]`

---

## 1. Bevezetés

### 1.1 A feladat
Egy PHP / HTML / CSS / JavaScript alapú webalkalmazás készítése a 7a — Front-controller tervezési minta gyakorlati anyag továbbfejlesztésével. Az alkalmazásnak meg kell felelnie a feladatkiírásban megadott funkcionális és pontozott követelményeknek.

### 1.2 Csapattagok és munkamegosztás
| Hallgató | Neptun | Elkészített feladatrészek |
|----------|--------|----------------------------|
| `[KITÖLTENDŐ]` | `[KITÖLTENDŐ]` | `[KITÖLTENDŐ — pl. Auth, CRUD, dokumentáció]` |
| `[KITÖLTENDŐ]` | `[KITÖLTENDŐ]` | `[KITÖLTENDŐ — pl. Frontend, Képek, Kapcsolat]` |

### 1.3 Élesített weboldal
**URL:** `[KITÖLTENDŐ: https://...]`

### 1.4 Forráskód
**GitHub repository (publikus):** `[KITÖLTENDŐ: https://github.com/.../...]`

### 1.5 Belépési adatok az ellenőrzéshez
> A tanári javításhoz szükséges hozzáférések. **Csak a beadandó PDF-ben szerepeljenek**, a Git-ben ne!

**FTP elérés:**
- Cím: `[KITÖLTENDŐ]`
- Felhasználónév: `[KITÖLTENDŐ]`
- Jelszó: `[KITÖLTENDŐ]`

**Adatbázis:**
- Host: `[KITÖLTENDŐ — pl. localhost]`
- Adatbázis neve: `[KITÖLTENDŐ]`
- Felhasználó: `[KITÖLTENDŐ]`
- Jelszó: `[KITÖLTENDŐ]`

**Teszt felhasználó az alkalmazásban:**
- Login: `[KITÖLTENDŐ]`
- Jelszó: `[KITÖLTENDŐ]`

---

## 2. Architektúra

### 2.1 Front-controller tervezési minta
Minden HTTP-kérés a `public/index.php` belépési ponton keresztül megy be, ahonnan a `src/Router.php` választja ki, melyik controller melyik akcióját kell meghívni. Ez biztosítja az URL-ek egységes kezelését és az autentikáció / közös logikák egy helyen tartását.

📷 *Képernyőkép helye: a `public/index.php` és `src/Router.php` szerkezete IDE-ben.*

### 2.2 Mappastruktúra
```
public/                  # webroot — csak ez érhető el a böngészőből
  index.php              # front-controller belépési pont
  .htaccess              # rewrite minden kérést index.php-re
  assets/css/, js/       # statikus erőforrások
  uploads/               # feltöltött képek (PHP futtatás itt tiltva)
src/
  Router.php             # útvonal-tábla és kérés-kezelés
  Controllers/           # menüpontonként egy controller
    Controller.php       # alaposztály (közös view + auth helpers)
    FoooldalController.php
    AuthController.php
    KepekController.php
    KapcsolatController.php
    UzenetekController.php
    KonyvekController.php
  Views/                 # template-ek
    layout.php           # közös layout (fejléc, lábrec, menü)
    fooldal.php
    auth/, kepek/, kapcsolat/, uzenetek/, konyvek/
config/
  db.php                 # PDO kapcsolat (élesben átírva)
sql/
  schema.sql             # adatbázis-séma
  seed.sql               # mintaadatok
```

### 2.3 Adatbázis-séma
Hat tábla: `users`, `uzenetek`, `kepek`, `kiadok`, `szerzok`, `konyvek`. Részletek: `sql/schema.sql`.

📷 *Képernyőkép helye: phpMyAdmin a táblákkal.*

### 2.4 Útvonalak (route-ok)
| Metódus | URL | Controller / Akció | Megjegyzés |
|---------|-----|--------------------|-----------|
| GET     | `/`                              | Foooldal · mutat                | Főoldal |
| GET     | `/belepes`                       | Auth · belepesUrlap             | Belépés/regisztráció |
| POST    | `/belepes`                       | Auth · belepes                  | Belépés feldolgozása |
| POST    | `/regisztracio`                  | Auth · regisztracio             | Új user (NEM léptet be) |
| POST    | `/kilepes`                       | Auth · kilepes                  | Session megsemmisítése (CSRF védett) |
| GET     | `/kepek`                         | Kepek · lista                   | Galéria |
| GET     | `/kepek/feltoltes`               | Kepek · feltoltesUrlap          | Csak belépve |
| POST    | `/kepek/feltoltes`               | Kepek · feltoltes               | Biztonságos upload |
| GET     | `/kapcsolat`                     | Kapcsolat · urlap               | Űrlap |
| POST    | `/kapcsolat`                     | Kapcsolat · kuldes              | Validál + DB-be ment + 5. oldal |
| GET     | `/uzenetek`                      | Uzenetek · lista                | Csak belépve |
| GET     | `/konyvek`                       | Konyvek · lista                 | Lista |
| GET     | `/konyvek/uj`                    | Konyvek · ujUrlap               |  |
| POST    | `/konyvek`                       | Konyvek · letrehoz              |  |
| GET     | `/konyvek/{id}/szerkesztes`      | Konyvek · szerkesztesUrlap      |  |
| POST    | `/konyvek/{id}/szerkesztes`      | Konyvek · frissit               |  |
| POST    | `/konyvek/{id}/torles`           | Konyvek · torles                |  |

---

## 3. Funkciók bemutatása

### 3.1 Reszponzív tervezés (3 pont)
A `public/assets/css/stilus.css` mobile-first felépítésű — alapból mobilra méretezett, `@media (min-width: 768px)` query-vel kapcsolódik be a desktop nézet (vízszintes menü, kétoszlopos főoldal, normál táblázat).

A táblázatok mobilon kártyásan jelennek meg (`@media (max-width: 600px)` blokk a `.tablazat` szabálynál) — minden cella `data-cim` attribútumot kap, ami pseudo-element-tel jeleníti meg a fejlécet.

📷 *Képernyőkép helye: ugyanaz az oldal desktop és mobil nézetben (DevTools mobile emulation).*

**Hol valósult meg:** `public/assets/css/stilus.css`

### 3.2 HTML5 + vízszintes menü (2 pont)
Az alkalmazás semantikus HTML5 elemeket használ: `<header>`, `<nav>`, `<main>`, `<footer>`, `<article>`, `<section>`, `<figure>`. A vízszintes menü desktopon `display: flex; flex-direction: row;`, mobilon hamburger-menüvel (`menu.js`) nyitható.

📷 *Képernyőkép helye: a vízszintes menü desktopon, és a hamburger-menü mobilon.*

**Hol valósult meg:** `src/Views/layout.php` (HTML5 váz), `public/assets/js/menu.js` (hamburger).

### 3.3 Regisztráció / Belépés / Kilépés (kötelező)
Egy oldalon (`/belepes`) található a bejelentkezés és a regisztráció űrlapja is, két oszlopban. **Regisztráció után az alkalmazás NEM lépteti be automatikusan** a felhasználót — a `flash` üzenet jelzi a sikert, majd a belépés űrlap marad. Ezt explicit a feladatkiírás kéri.

#### Jelszókezelés
A jelszavakat soha nem tároljuk plain szövegként:
```php
// Regisztráció
$stmt->execute([
    ':jelszo_hash' => password_hash($jelszo, PASSWORD_DEFAULT),
    // ...
]);

// Belépés
if (!password_verify($jelszo, $user['jelszo_hash'])) {
    $hibak['altalanos'] = 'Hibás felhasználónév vagy jelszó.';
}
```
A `PASSWORD_DEFAULT` algoritmus a PHP verzió aktuális ajánlása szerint változik (jelenleg bcrypt). Az ellenőrzés `password_verify`-jal időegyensúlyos, time-attack ellen védett.

#### Session-kezelés
Sikeres belépéskor:
```php
session_regenerate_id(true);   // session fixation védelem
$_SESSION['felhasznalo_id']          = (int) $user['id'];
$_SESSION['felhasznalo_login']       = $user['login'];
$_SESSION['felhasznalo_csaladi_nev'] = $user['csaladi_nev'];
$_SESSION['felhasznalo_uton_nev']    = $user['uton_nev'];
```
A fejlécen bejelentkezve megjelenik: `Bejelentkezett: <Családi_név> <Utónév> (<Login_név>)` — pontosan ahogy a feladatkiírás megadja.

#### Kilépés POST-os
Eredetileg a `/kilepes` GET volt, de ez sebezhetőséget jelentett: egy idegen oldal egy `<img src="/kilepes">` tagje is kiléptette volna a felhasználót. Ezért áttértünk POST-ra, CSRF tokennel védve. A layout fejlécében egy form-os gombként jelenik meg.

📷 *Képernyőképek: belépés űrlap, sikeres regisztráció flash üzenete, fejléc bejelentkezett állapotban, kilépés gomb.*

**Hol valósult meg:** `src/Controllers/AuthController.php`, `src/Views/auth/belepes.php`, `src/Views/layout.php`.

### 3.4 Főoldal — saját videó, YouTube, Google térkép (3 pont)
A főoldal négy tartalmi szakaszból áll:
1. **Hero szekció** — bemutatja az alkalmazás célját, témához illő szöveggel.
2. **Bemutató szekció** — magyarázza, mit lehet csinálni az oldalon.
3. **Két videó egymás mellett (responsive grid):**
   - Helyi `<video>` tag a `public/assets/video/sajat.mp4` fájlból (≤5 mp, méretkorlát miatt rövid).
   - YouTube `<iframe>` embed az **Országos Széchényi Könyvtár** bemutató videójához (`davY52_DFrw` videó-ID), `youtube-nocookie.com` domain-en — így nem helyez el hirdetés-sütit a felhasználón.
4. **Google térkép** `<iframe>` embed az **Országos Széchényi Könyvtár** címére (Budapest, Szent György tér 4–6), így a "fizikai cím" témához illő.

```html
<iframe
    src="https://www.youtube-nocookie.com/embed/davY52_DFrw"
    title="OSZK — Országos Széchényi Könyvtár bemutató"
    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
    allowfullscreen></iframe>
```

📷 *Képernyőkép helye: a teljes főoldal görgetve, hogy a térkép is látszódjon.*

**Hol valósult meg:** `src/Views/fooldal.php`.

### 3.5 Képek menü — galéria + feltöltés (3 pont)
A galéria (`/kepek`) bárki számára elérhető, és a `kepek` táblából tölti be a feltöltött képeket. **Új kép feltöltés csak bejelentkezett user számára engedélyezett** (`csakBelepve()` ellenőrzés a controllerben).

#### Galéria megjelenítés
A galéria CSS Grid-del rendezi a képeket reszponzív kártyákká:
```css
.galeria {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}
```
A `auto-fill` és `minmax(150px, 1fr)` együttesen automatikusan annyi oszlopot rak ki, amennyi befér — desktopon több oszlop, mobilon 1-2.

#### Feltöltés biztonsági lépésről-lépésre
A `KepekController::feltoltes()` metódusban több ellenőrzés fut:

1. **CSRF token ellenőrzés** (`csrfEllenoriz()`).
2. **Bejelentkezés ellenőrzés** (`csakBelepve()`).
3. **Fájl jelenléte és sikeres feltöltés:** `$_FILES['kep']['error'] === UPLOAD_ERR_OK`.
4. **Méret-limit** (5 MB):
   ```php
   if ($merete > self::MAX_MERET_BAJT) {
       $hibak['kep'] = 'A fájl mérete nem lehet több, mint 5 MB.';
   }
   ```
5. **MIME-típus a fájl tartalmából** (NEM a kliens által küldött `$_FILES['type']`-ból, mert azt a kliens szabadon hazudja):
   ```php
   $finfo = new \finfo(FILEINFO_MIME_TYPE);
   $mime  = $finfo->file($_FILES['kep']['tmp_name']);
   if (!isset(self::ENGEDELYEZETT_MIME[$mime])) {
       $hibak['kep'] = 'Csak JPG, PNG, GIF vagy WEBP képeket lehet feltölteni.';
   }
   ```
6. **Generált fájlnév** — soha nem a user által megadottat használjuk, így path traversal (`..`, `/`, `\`) kizárt:
   ```php
   $generaltNev = bin2hex(random_bytes(16)) . '.' . $kiterjesztes;
   ```
7. A `public/uploads/.htaccess` további védőréteg: ott a webszerver nem hajt végre PHP-t és nem listázza a könyvtárat:
   ```apache
   <FilesMatch "\.(php|phtml|phar|inc)$">
       Require all denied
   </FilesMatch>
   Options -Indexes -ExecCGI
   ```

📷 *Képernyőkép helye: galéria nézet, feltöltés űrlap, sikeres feltöltés.*

**Hol valósult meg:** `src/Controllers/KepekController.php`, `src/Views/kepek/`, `public/uploads/.htaccess`.

### 3.6 Kapcsolat menü — űrlap + kétoldali validáció + DB mentés (2 pont)
Az űrlap **`novalidate`** attribútummal indul — szándékosan nem használunk HTML5 validátor attribútumokat (`required`, `pattern` stb.), ahogy a feladatkiírás explicit előírja:
```html
<form id="kapcsolatUrlap" class="urlap" method="POST" action="/kapcsolat" novalidate>
    <input type="hidden" name="_csrf" value="<?= $h($csrf_token) ?>">
    <div class="urlap-mezo">
        <label for="nev">Név</label>
        <input type="text" id="nev" name="nev" value="<?= $h($mezok['nev']) ?>">
        <div class="urlap-hibauzenet" id="hiba_nev"><?= $h($hibak['nev'] ?? '') ?></div>
    </div>
    <!-- ... -->
</form>
```

#### Kliens-oldali validáció (`public/assets/js/kapcsolat.js`)
A JavaScript blur-on és submit-on is validál:
```javascript
function validal() {
    const hibak = {};
    const nev = mezok.nev.value.trim();
    if (nev.length < 2) {
        hibak.nev = 'A név legalább 2 karakter legyen.';
    }
    const email = mezok.email.value.trim();
    if (email === '') {
        hibak.email = 'Az e-mail cím megadása kötelező.';
    } else if (!ervenyesEmail(email)) {
        hibak.email = 'Érvényes e-mail címet adj meg.';
    }
    // ... tárgy és üzenet ellenőrzés
    return hibak;
}

urlap.addEventListener('submit', function (esemeny) {
    const hibak = validal();
    hibakKiir(hibak);
    if (Object.keys(hibak).length > 0) {
        esemeny.preventDefault();
        // első hibás mezőre fókusz
    }
});
```

#### Szerver-oldali validáció (`KapcsolatController::validal()`)
A szerver mindig megismétli a teljes ellenőrzést — JS-t kikapcsolva (vagy automatizált POST-tal) is védett az alkalmazás:
```php
private function validal(array $mezok): array
{
    $hibak = [];
    if ($mezok['nev'] === '' || mb_strlen($mezok['nev']) < 2) {
        $hibak['nev'] = 'A név megadása kötelező (legalább 2 karakter).';
    }
    if ($mezok['email'] === '') {
        $hibak['email'] = 'Az e-mail cím megadása kötelező.';
    } elseif (!filter_var($mezok['email'], FILTER_VALIDATE_EMAIL)) {
        $hibak['email'] = 'Érvényes e-mail címet adj meg.';
    }
    // ... tárgy és üzenet ellenőrzés azonos logikával
    return $hibak;
}
```

#### Sikeres küldés folyamata
1. **CSRF token + validáció áthalad.**
2. **Mentés az `uzenetek` táblába** prepared statement-tel:
   ```php
   $stmt = $this->dbh->prepare(
       'INSERT INTO uzenetek (user_id, nev, email, targy, uzenet)
        VALUES (:user_id, :nev, :email, :targy, :uzenet)'
   );
   $stmt->execute([...]);
   ```
   Ha bejelentkezett user küldte, a `user_id` is rögzül.
3. **5. oldal** megjelenítése (`kapcsolat/siker.php`) a beküldött adatokkal.

📷 *Képernyőképek: kapcsolat űrlap, kliens-oldali hibaüzenet (JS), szerver-oldali hibaüzenet (JS kikapcsolva), sikeres küldés visszajelzése.*

**Hol valósult meg:** `src/Controllers/KapcsolatController.php`, `src/Views/kapcsolat/`, `public/assets/js/kapcsolat.js`.

### 3.7 Üzenetek menü (2 pont)
**Csak bejelentkezett user** férhet hozzá. A `csakBelepve()` segéd-metódus a `Controller` alaposztályban ellenőrzi a session-t.

A táblázat `ORDER BY u.kuldve DESC` rendezéssel jelenik meg — **fordított időrend**, a legfrissebb felül. Ha a küldő bejelentkezett user volt, a neve+login jelenik meg; ha vendég, akkor `"Vendég — <megadott név>"`.

📷 *Képernyőkép helye: az üzenetek táblázat töltött állapotban.*

**Hol valósult meg:** `src/Controllers/UzenetekController.php`, `src/Views/uzenetek/lista.php`.

### 3.8 CRUD modul — Könyvek (5 pont)
A `konyvek` táblát kezeli teljes körűen, **útvonalakkal**:

| Metódus | URL | Akció | Védelem |
|---------|-----|-------|---------|
| GET     | `/konyvek`                    | lista (publikus, kereshető)        | — |
| GET     | `/konyvek/uj`                 | új könyv űrlap                      | csak belépve |
| POST    | `/konyvek`                    | új könyv mentése                    | csak belépve + CSRF |
| GET     | `/konyvek/{id}/szerkesztes`   | szerkesztő űrlap                    | csak belépve |
| POST    | `/konyvek/{id}/szerkesztes`   | frissítés mentése                   | csak belépve + CSRF |
| POST    | `/konyvek/{id}/torles`        | törlés                               | csak belépve + CSRF |

#### Lista — keresés és szűrés
A lista nézet támogat szabadszöveges keresést (cím / szerző / kiadó alapján) és műfaj-szerinti szűrést, GET paraméterekkel. A SQL dinamikusan épül, de minden user-érték prepared statement paraméteren keresztül megy be:
```php
$sql = "SELECT k.id, k.cim, ... FROM konyvek k
        LEFT JOIN szerzok sz ON sz.id = k.szerzo_id
        LEFT JOIN kiadok  ki ON ki.id = k.kiado_id
        WHERE 1 = 1";

if ($kereses !== '') {
    $sql .= " AND (k.cim LIKE :kereses OR sz.csaladi_nev LIKE :kereses ...)";
    $parameterek[':kereses'] = '%' . $kereses . '%';
}
if ($mufaj !== '') {
    $sql .= " AND k.mufaj = :mufaj";
    $parameterek[':mufaj'] = $mufaj;
}

$stmt = $this->dbh->prepare($sql);
$stmt->execute($parameterek);
```

#### Validáció
Az űrlap szerver-oldali validációja a `validal()` metódusban:
- **Cím:** kötelező, 2–200 karakter
- **Megjelenés éve:** ha megadott, érvényes tartomány (1000 — jövő év + 1)
- **Oldalszám:** ha megadott, pozitív és értelmes (max 100 000)
- **Műfaj:** legfeljebb 60 karakter

#### Törlés véletlen kattintás ellen
A lista nézet törlés-gombja egy form-ot küld POST-tal és `confirm()` JS dialógusra van kötve:
```html
<form method="POST" action="/konyvek/<?= (int) $k['id'] ?>/torles"
      onsubmit="return confirm('Biztosan törlöd a következő könyvet: ...?');">
    <input type="hidden" name="_csrf" value="<?= $h($csrf_token) ?>">
    <button type="submit" class="gomb gomb-veszelyes">Törlés</button>
</form>
```

📷 *Képernyőképek: lista nézet, keresés/szűrés használat közben, új könyv létrehozása, szerkesztés, törlés megerősítés.*

**Hol valósult meg:** `src/Controllers/KonyvekController.php`, `src/Views/konyvek/`.

### 3.9 Internetes tárhely (3 pont)
**Tárhely-szolgáltató:** `[KITÖLTENDŐ — pl. Nethely, 000webhost, stb.]`
**Élesített URL:** `[KITÖLTENDŐ]`

A tárhelyen a `config/db.php` host/dbname/user/jelszó értékeit a szolgáltató által megadott adatokra cseréltük.

📷 *Képernyőkép helye: az élesben futó alkalmazás főoldala a tárhelyen, és a tárhely admin felülete.*

### 3.10 GitHub verziókövetés (2 pont)
**Repository URL:** `[KITÖLTENDŐ]`

A fejlesztés legalább 5 részállapotban történt, időben elosztva — a commit-üzenetek beszédesek, magyarul.

📷 *Képernyőkép helye: GitHub commit history teljes nézete.*

### 3.11 GitHub projektmunka (3 pont — opcionális)
A feladatkiírás szerint, ha a hallgató egyedül készíti a beadandót, a projektmunka 3 pontjáért két GitHub-fiókot kell használni. Ha egy fiókkal commit-olunk, a 3 pontot lemondjuk — a maximum 27 pont marad elérhető.

`[KITÖLTENDŐ — döntsd el a véglegesítés előtt: két fiókkal mész (és kiosztod, ki mit committol), vagy egy fiókkal és lemondasz a 3 pontról]`

📷 *Képernyőkép helye (ha két fiók): GitHub Contributors / Insights oldal vagy az egyéni commit history-k.*

### 3.12 Biztonsági réteg (keresztmetszetben)
A pontozott funkciókon átívelő, minden ponton érvényesülő védelmek listája. Ezek nem szerepelnek a feladatkiírásban explicit pontként, viszont a tanári ellenőrzéskor relevánsak, és a publikus tárhelyre szánt alkalmazástól minimumkövetelmények.

#### SQL injection elleni védelem
Az alkalmazás **minden** adatbázis-művelete prepared statement-tel fut, paraméter-bindinggal. A PDO-t `PDO::ATTR_EMULATE_PREPARES => false` módban inicializáljuk, így a paraméterek a szerver oldalán kerülnek behelyettesítésre:
```php
$dbh = new PDO(..., [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
```
Sehol nem fűzünk user-input értéket SQL-be string-konkatenációval.

#### XSS elleni védelem
Minden view-ban egy közös rövid lambda escapeli a kimeneteket:
```php
$h = static fn(?string $s): string => htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
```
Az `ENT_QUOTES` mind az egyszeres, mind a dupla idézőjeleket escapeli, az `UTF-8` charset paraméter pedig védi a többbájtos karaktereket. A `<?= $h($valami) ?>` minta minden felhasználói érték HTML-be írásánál kötelező.

#### CSRF védelem
Minden POST végpont CSRF-tokennel védett. A token a session életciklusa alatt állandó, 64 hexa karakteres random string (`bin2hex(random_bytes(32))`). Az ellenőrzés időegyensúlyos:
```php
if (!hash_equals($eltarolt, $beerkezett)) {
    http_response_code(419);
    $this->flash('hiba', 'A biztonsági token érvénytelen vagy lejárt.');
    $this->atiranyit('/');
}
```

#### Session védelem
- **Session fixation ellen:** belépéskor `session_regenerate_id(true)`.
- **Cookie flag-ek** (`public/index.php`-ben, a `session_start()` ELŐTT):
  - `httponly: true` — JS-ből nem olvasható (XSS esetén is védett)
  - `samesite: Lax` — cross-site POST-ot blokkolja
  - `secure: $httpsAktiv` — HTTPS-en csak titkosított csatornán megy
- **Kilépéskor:** `$_SESSION = []; session_destroy();` és a session cookie is törlődik.

#### Jelszókezelés
- Tárolás: `password_hash($jelszo, PASSWORD_DEFAULT)` (jelenleg bcrypt 10 cost factor).
- Ellenőrzés: `password_verify($input, $hash)` — időegyensúlyos.
- Minimum jelszóhossz: 8 karakter.
- Sehol nincs `md5`, `sha1` vagy plain text jelszó.

#### Fájlfeltöltés védelem
Részletek a 3.5 szekcióban. Röviden: MIME-típus a fájl tartalmából (`finfo`), kiterjesztés whitelist, méret-limit, generált fájlnév, `.htaccess` letiltja a PHP-t és a directory listing-et.

#### Biztonsági HTTP fejlécek
A `public/index.php` minden válaszra beállítja:
```php
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

#### Hibaüzenetek éles módban
A `display_errors` csak localhost-on van bekapcsolva, élesben minden hiba a szerver hibanaplójába kerül a `log_errors`-on keresztül. A felhasználó soha nem lát stack trace-t vagy adatbázis-hibaüzenetet.

#### Hitelesítő adatok kiszivárgása ellen
A `config/db.php` üres alapértékekkel kerül a Git-be. A tényleges hitelesítők vagy:
- `config/db.local.php`-be kerülnek (Git-ből kizárva), vagy
- a tárhelyen kerülnek beírásra a deploy után.

A `.gitignore` kizár minden érzékeny dolgot: `db.local.php`, feltöltött képek, `.claude/`, projekt-belső docs.

📷 *Képernyőkép helye (opcionális): GitHub repo, ahol látszik, hogy a `config/db.php` nem tartalmaz valódi hitelesítőket.*

---

## 4. Csapatmunka részletei

### 4.1 `[KITÖLTENDŐ — Hallgató 1 neve]` hozzájárulása
- `[KITÖLTENDŐ — pl. Front-controller, Router, Auth, Üzenetek menü]`
- `[KITÖLTENDŐ — kb. hány commit, milyen funkciók]`

### 4.2 `[KITÖLTENDŐ — Hallgató 2 neve]` hozzájárulása
- `[KITÖLTENDŐ — pl. Frontend (CSS+JS), Képek menü, Kapcsolat-űrlap, dokumentáció]`
- `[KITÖLTENDŐ — kb. hány commit, milyen funkciók]`

---

## 5. Összegzés

A projekt során elsajátítottuk a front-controller tervezési minta gyakorlati alkalmazását, a PDO prepared statement-eken keresztüli biztonságos adatbázis-elérést, a kétoldali űrlap-validációt, valamint a reszponzív CSS mobile-first felépítését. A csapatmunka során a Git workflow-t is begyakoroltuk.

`[KITÖLTENDŐ — saját tapasztalatok, mit tanultatok, mi volt a legnehezebb, mit fejlesztenétek tovább.]`

---

## 6. Mellékletek

- `sql/schema.sql` — adatbázis-séma
- `sql/seed.sql` — mintaadatok
- A teljes forráskód a GitHub repositoryban érhető el.

---

## 7. Tesztelési útmutató

Az alábbi forgatókönyvekkel ellenőrizhető, hogy az alkalmazás minden funkciója a feladatkiírás szerint működik. A javító tanár ezeket a lépéseket végrehajtva tudja igazolni a pontok teljesülését.

### 7.1 Helyi telepítés
1. Telepítsd PHP 7.4+-t és MySQL/MariaDB-t (XAMPP / WAMP / Laragon megfelelő).
2. phpMyAdmin-ban hozz létre egy `webprog_konyvtar` nevű adatbázist (utf8mb4_unicode_ci).
3. Importáld be a `sql/schema.sql` és `sql/seed.sql` fájlokat.
4. Másold a `config/db.php`-t `config/db.local.php` néven, és írd be a saját DB-hozzáféréseidet.
5. Indítsd a beépített PHP fejlesztői szervert: `php -S localhost:8000 -t public`
6. Nyisd meg böngészőben: <http://localhost:8000>

### 7.2 Tesztforgatókönyvek

#### TK1 — Regisztráció
1. Nyisd a `/belepes` oldalt.
2. A jobb oldali "Regisztráció" űrlapba írj be: új login (legalább 3 karakter), email, családi név, utónév, jelszó (legalább 8 karakter) kétszer.
3. Kattints a "Regisztráció" gombra.
4. **Várt eredmény:** zöld flash üzenet ("Sikeres regisztráció! Most már bejelentkezhetsz."). A user **NEM** lép be automatikusan — ez explicit követelmény.
5. **Ellenőrzés:** phpMyAdmin-ban a `users` tábla új sort tartalmaz `jelszo_hash` mezővel (NEM plain text).

#### TK2 — Belépés és kilépés
1. Bejelentkezés a most regisztrált adatokkal a `/belepes` oldal bal űrlapján.
2. **Várt eredmény:** átirányítás a főoldalra, fejlécen `Bejelentkezett: <Családi név> <Utónév> (<Login>)` szöveg, menüből eltűnik a "Bejelentkezés", megjelenik a "Üzenetek" és "Kilépés".
3. Kattints a "Kilépés" gombra.
4. **Várt eredmény:** átirányítás a főoldalra, "Sikeresen kijelentkeztél" flash, fejléc visszaáll a kijelentkezett állapotra.

#### TK3 — Hibás belépés
1. Próbálj bejelentkezni rossz jelszóval.
2. **Várt eredmény:** "Hibás felhasználónév vagy jelszó" hibaüzenet, az űrlap kitöltött loginnel marad (de jelszó nélkül).

#### TK4 — Reszponzív viselkedés
1. Nyisd meg DevTools-t (F12), válts mobil emulációra (pl. iPhone SE).
2. **Várt eredmény:** a vízszintes menü eltűnik, helyette hamburger ikon jelenik meg. Rákattintva legördül a menü.
3. Térj vissza desktop méretre — visszajön a vízszintes menü.

#### TK5 — Kapcsolat-űrlap kliens-oldali validáció
1. Nyisd a `/kapcsolat` oldalt.
2. Hagyd üresen a Név mezőt és kattints a beküldés gombra.
3. **Várt eredmény:** a kliens-oldali JS megjeleníti "A név legalább 2 karakter legyen." hibát, az űrlap **NEM** küldődik be (semmi nem kerül a `uzenetek` táblába).

#### TK6 — Kapcsolat-űrlap szerver-oldali validáció
1. Kapcsold ki a JavaScriptet (DevTools → Settings → Disable JavaScript).
2. Töltsd ki az űrlapot **érvénytelen** email-lel (pl. `nemEmail`) és küldd be.
3. **Várt eredmény:** a szerver-oldali validáció lekapja a hibát, ugyanaz a hibaüzenet jelenik meg PHP-ből is. Ezzel bizonyítjuk, hogy a védelem nem csak JS-en múlik.

#### TK7 — Kapcsolat sikeres küldés
1. Töltsd ki helyesen az űrlapot, küldd be.
2. **Várt eredmény:** átirányít az 5. oldalra ("Köszönjük az üzenetet!") a beküldött adatok visszamutatásával.
3. **Ellenőrzés:** phpMyAdmin-ban az `uzenetek` tábla új sort tartalmaz.

#### TK8 — Üzenetek menü
1. Belépve nyisd a `/uzenetek` oldalt.
2. **Várt eredmény:** táblázat az `uzenetek`-ből, fordított időrendben (legfrissebb felül).
3. Próbáld kijelentkezve elérni a `/uzenetek`-et.
4. **Várt eredmény:** átirányít a `/belepes`-re hibaüzenettel.

#### TK9 — Képfeltöltés
1. Belépve nyisd a `/kepek/feltoltes` oldalt.
2. Tölts fel egy normál JPG / PNG képet (max 5 MB).
3. **Várt eredmény:** sikeres feltöltés, a galériában megjelenik. A `public/uploads/` mappában a fájl `<hex>.<kit>` formátumú generált névvel látható (NEM az eredetivel).
4. Próbálj feltölteni egy `.txt` fájlt vagy egy 6 MB-os képet.
5. **Várt eredmény:** szerver-oldali hibaüzenet, a fájl nem mentődik le.

#### TK10 — CRUD: lista + keresés
1. Nyisd a `/konyvek` oldalt (kijelentkezett állapotban is).
2. **Várt eredmény:** 10 mintakönyv listája. Az "Új könyv hozzáadása" gomb és a per-soros Szerkesztés/Törlés **nem** látszik (nincs belépve).
3. Írj be a keresőbe: "Tolkien". Kattints "Szűrés".
4. **Várt eredmény:** csak Tolkien könyvei jelennek meg, a találatszám visszaköszön.

#### TK11 — CRUD: létrehozás / szerkesztés / törlés
1. Belépve nyisd a `/konyvek` oldalt — most látszik az "+ Új könyv hozzáadása" gomb és a Szerkesztés/Törlés is.
2. Kattints az "Új könyv" gombra, töltsd ki, mentsd.
3. **Várt eredmény:** flash üzenet, új könyv a listán.
4. Szerkeszd, módosítsd a címet, mentsd.
5. **Várt eredmény:** flash, módosított cím a listán.
6. Töröld — `confirm()` dialógus, megerősítés után törlődik.

#### TK12 — 404 oldal
1. Nyiss egy nem létező URL-t, pl. `/nincs-ilyen`.
2. **Várt eredmény:** "404 — Nincs ilyen oldal" oldal, közepre rendezett, "Vissza a főoldalra" és "Könyvek megtekintése" gombokkal.

#### TK13 — CSRF védelem
1. Nyisd meg a kapcsolat-űrlapot DevTools-ban, töröld ki a `_csrf` rejtett input értékét, próbáld beküldeni.
2. **Várt eredmény:** a szerver elveti, "biztonsági token érvénytelen" üzenet a főoldalra irányítva.

#### TK14 — Session security
1. Belépés után a DevTools → Application → Cookies-ban ellenőrizd a `PHPSESSID` cookie-t.
2. **Várt eredmény:** `HttpOnly` és `SameSite=Lax` flag-ek be vannak állítva. Localhost-on `Secure` nincs (HTTP), élesben (HTTPS) `Secure` is be lesz kapcsolva.

---

## Még szükséges képernyőképek (teendőlista)

- [ ] Főoldal desktop nézet
- [ ] Főoldal mobil nézet (DevTools mobile emulation)
- [ ] Hamburger-menü mobilon, kinyitva
- [ ] Bejelentkezés űrlap üresen
- [ ] Bejelentkezés űrlap hibás adattal (validációs üzenet)
- [ ] Sikeres regisztráció flash üzenet
- [ ] Bejelentkezett fejléc, ahol látszik a "Bejelentkezett: ..."
- [ ] Képgaléria
- [ ] Képfeltöltés űrlap
- [ ] Képfeltöltés sikeres visszajelzés
- [ ] Kapcsolat űrlap kliens-oldali validációs hiba (élő, blur-on)
- [ ] Kapcsolat űrlap szerver-oldali hiba (JS kikapcsolva, hibás email)
- [ ] Kapcsolat sikeres küldés — 5. oldal
- [ ] Üzenetek menü táblázat
- [ ] Könyvek lista (CRUD)
- [ ] Új könyv felvétele űrlap
- [ ] Könyv szerkesztése űrlap
- [ ] Törlés confirm dialógus
- [ ] phpMyAdmin a táblákkal
- [ ] GitHub commit history
- [ ] GitHub Contributors oldal
- [ ] Élesített weboldal főoldala (a tárhelyen)
- [ ] Tárhely admin felülete

---

**Fájlnév-konvenció a leadáshoz:** `Név-NeptunKód.pdf` (pl. `KovacsFerenc-ABC123.pdf`).
**Mindkét csapattagnak külön be kell adnia a saját PDF-jét** Teams-en.
