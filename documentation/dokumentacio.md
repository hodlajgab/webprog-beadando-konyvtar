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
| GET     | `/kilepes`                       | Auth · kilepes                  | Session megsemmisítése |
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
Egy oldalon (`/belepes`) található a bejelentkezés és a regisztráció űrlapja is, két oszlopban. **Regisztráció után az alkalmazás NEM lépteti be automatikusan** a felhasználót — a `flash` üzenet jelzi a sikert, majd a belépés űrlap maradja.

A jelszavakat `password_hash($jelszo, PASSWORD_DEFAULT)` tárolja, a belépés `password_verify`-jal ellenőrzi. Sikeres belépéskor `session_regenerate_id(true)` fut le (session fixation védelem).

A fejlécen bejelentkezve megjelenik: `Bejelentkezett: <Családi_név> <Utónév> (<Login_név>)`.

📷 *Képernyőképek: belépés űrlap, sikeres regisztráció flash üzenete, fejléc bejelentkezett állapotban.*

**Hol valósult meg:** `src/Controllers/AuthController.php`, `src/Views/auth/belepes.php`, `src/Views/layout.php`.

### 3.4 Főoldal — saját videó, YouTube, Google térkép (3 pont)
A főoldal három tartalmi szakaszból áll:
1. **Hero szekció** — bemutatja az alkalmazás célját.
2. **Két videó egymás mellett (responsive grid):**
   - Helyi `<video>` tag a `public/assets/video/sajat.mp4` fájlból (≤5 mp).
   - YouTube `<iframe>` embed.
3. **Google térkép** `<iframe>` embed a Múzeum krt. 14 koordinátáira (példa cím — a véglegesítéskor a tényleges címre cseréld).

📷 *Képernyőkép helye: a teljes főoldal görgetve, hogy a térkép is látszódjon.*

**Hol valósult meg:** `src/Views/fooldal.php`.

### 3.5 Képek menü — galéria + feltöltés (3 pont)
A galéria (`/kepek`) bárki számára elérhető, és a `kepek` táblából tölti be a feltöltött képeket. **Új kép feltöltés csak bejelentkezett user számára engedélyezett** (`csakBelepve()` ellenőrzés a controllerben).

A feltöltés biztonsági szempontból:
- MIME-típus tényleges fájltartalomból (`finfo_file`) — NEM a kliens által küldött `$_FILES['type']`-ból.
- Kiterjesztés whitelist: jpg, png, gif, webp.
- Méret-limit: 5 MB.
- A mentett fájl neve **generált** (`bin2hex(random_bytes(16))` + a kiterjesztés), így path traversal kizárt.
- A `public/uploads/.htaccess` letiltja a PHP-futtatást a feltöltési mappában.

📷 *Képernyőkép helye: galéria nézet, feltöltés űrlap, sikeres feltöltés.*

**Hol valósult meg:** `src/Controllers/KepekController.php`, `src/Views/kepek/`, `public/uploads/.htaccess`.

### 3.6 Kapcsolat menü — űrlap + kétoldali validáció + DB mentés (2 pont)
Az űrlap **`novalidate`** attribútummal indul — szándékosan nem használunk HTML5 validátor attribútumokat (`required`, `pattern` stb.), ahogy a feladatkiírás előírja.

**Kliens-oldali validáció:** `public/assets/js/kapcsolat.js`
- Minden mezőre blur-on élő visszajelzés.
- Submit-on újra teljes körű ellenőrzés; ha hiba van, `e.preventDefault()` és az első hibás mezőre fókusz.

**Szerver-oldali validáció:** `KapcsolatController::validal()`
- Név (2–100 karakter), e-mail (`FILTER_VALIDATE_EMAIL`), tárgy (3–150), üzenet (10–5000).

Sikeres küldés:
1. Mentés az `uzenetek` táblába (prepared statement).
2. **5. oldal** (`kapcsolat/siker.php`) megjelenítése a beküldött adatokkal.

📷 *Képernyőképek: kapcsolat űrlap, kliens-oldali hibaüzenet (JS), szerver-oldali hibaüzenet (JS kikapcsolva), sikeres küldés visszajelzése.*

**Hol valósult meg:** `src/Controllers/KapcsolatController.php`, `src/Views/kapcsolat/`, `public/assets/js/kapcsolat.js`.

### 3.7 Üzenetek menü (2 pont)
**Csak bejelentkezett user** férhet hozzá. A `csakBelepve()` segéd-metódus a `Controller` alaposztályban ellenőrzi a session-t.

A táblázat `ORDER BY u.kuldve DESC` rendezéssel jelenik meg — **fordított időrend**, a legfrissebb felül. Ha a küldő bejelentkezett user volt, a neve+login jelenik meg; ha vendég, akkor `"Vendég — <megadott név>"`.

📷 *Képernyőkép helye: az üzenetek táblázat töltött állapotban.*

**Hol valósult meg:** `src/Controllers/UzenetekController.php`, `src/Views/uzenetek/lista.php`.

### 3.8 CRUD modul — Könyvek (5 pont)
A `konyvek` táblát kezeli teljes körűen, **útvonalakkal**:
- `GET  /konyvek` — lista
- `GET  /konyvek/uj` — új könyv űrlap
- `POST /konyvek` — létrehozás
- `GET  /konyvek/{id}/szerkesztes` — szerkesztő űrlap
- `POST /konyvek/{id}/szerkesztes` — frissítés
- `POST /konyvek/{id}/torles` — törlés

Az űrlap szerver-oldali validációja a `validal()` metódusban: cím kötelező, év érvényes tartomány, oldalszám pozitív szám.

A törlés `confirm()` JS dialógussal véd véletlen kattintás ellen.

📷 *Képernyőképek: lista nézet, új létrehozása, szerkesztés, törlés megerősítés.*

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

### 3.11 GitHub projektmunka (3 pont)
A két csapattag külön Git identitással commit-olt; a GitHub commit history-ban látszik, ki melyik résznek a szerzője.

📷 *Képernyőkép helye: GitHub Contributors / Insights oldal vagy az egyéni commit history-k.*

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
