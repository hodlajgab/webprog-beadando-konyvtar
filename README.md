# Könyvtár — Web-programozás 1 beadandó

PHP / HTML / CSS / JavaScript alapú webalkalmazás könyvtári adatbázis kezeléshez.
A 7a. gyakorlat _Front-controller tervezési minta (2. Megoldás)_ továbbfejlesztése.

## Funkciók

- Regisztráció, belépés, kilépés (jelszó-hash, session)
- Főoldal (saját videó, YouTube embed, Google térkép)
- Képgaléria + biztonságos képfeltöltés (csak belépve)
- Kapcsolat-űrlap kliens (JS) és szerver (PHP) oldali validációval
- Üzenetek menü — beérkezett üzenetek listázása (csak belépve)
- CRUD modul a `konyvek` táblához (lista / új / szerkesztés / törlés útvonalakkal)
- Reszponzív tervezés mobile-first CSS-sel

## Helyi telepítés

1. PHP 7.4+ és MySQL/MariaDB szükséges.
2. Hozz létre egy `webprog_konyvtar` nevű adatbázist:
   ```sql
   CREATE DATABASE webprog_konyvtar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
3. Importáld a sémát és a mintaadatokat:
   ```bash
   mysql -u root -p webprog_konyvtar < sql/schema.sql
   mysql -u root -p webprog_konyvtar < sql/seed.sql
   ```
4. Másold a `config/db.php`-t `config/db.local.php` néven, és írd be a saját DB-hozzáféréseidet (a `db.local.php` `.gitignore`-olva van).
5. Indítsd a beépített PHP fejlesztői szervert:
   ```bash
   php -S localhost:8000 -t public
   ```
6. Nyisd meg: <http://localhost:8000/>

## Mappastruktúra

```
public/        webroot — front-controller (index.php), assets/, uploads/
src/
  Router.php
  Controllers/ menüpontonkénti controllerek
  Views/       PHP template-ek
config/        DB-konfiguráció
sql/           schema.sql, seed.sql
documentation/ a 15+ oldalas PDF vázlata Markdown-ban
```

## Éles tárhelyre telepítés

A `config/db.php` legfelső sorait írd át a tárhely-szolgáltató által adott értékekre (host, db neve, user, jelszó). Töltsd fel a teljes projektet (a `.gitignore` által kihagyott fájlok nélkül). A `public/` legyen a webroot — ha a szolgáltató nem támogatja másik webroot beállítását, a `public/` tartalmát a `public_html/`-be másold át, és a `require __DIR__ . '/../config/db.php'` útvonalat igazítsd.

## Készítette

- `[Hallgató 1 neve – Neptun]`
- `[Hallgató 2 neve – Neptun]`
