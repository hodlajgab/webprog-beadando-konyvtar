-- ================================================================
-- Web-programozás 1 beadandó — adatbázis-séma (MySQL)
-- Téma: Könyvtár / Könyvek
-- ================================================================
--
-- Használat:
--   1) phpMyAdmin-ban hozz létre egy 'webprog_konyvtar' nevű adatbázist
--      (utf8mb4_unicode_ci összevetéssel)
--   2) Importáld be ezt a schema.sql-t
--   3) Opcionálisan importáld be a seed.sql-t mintaadatokkal
--
-- A táblák utf8mb4 kódolásúak — ékezetes karakterek és emoji is fér be.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS uzenetek;
DROP TABLE IF EXISTS kepek;
DROP TABLE IF EXISTS konyvek;
DROP TABLE IF EXISTS szerzok;
DROP TABLE IF EXISTS kiadok;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- Felhasználók
-- ================================================================
CREATE TABLE users (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    login         VARCHAR(50)  NOT NULL UNIQUE,
    jelszo_hash   VARCHAR(255) NOT NULL,
    csaladi_nev   VARCHAR(50)  NOT NULL,
    uton_nev      VARCHAR(50)  NOT NULL,
    email         VARCHAR(120) NOT NULL UNIQUE,
    letrehozva    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Kapcsolat-űrlapon érkezett üzenetek
-- ================================================================
CREATE TABLE uzenetek (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NULL,                            -- ha bejelentkezett user küldte
    nev        VARCHAR(100) NOT NULL,                        -- "Vendég" ha nem belépett
    email      VARCHAR(120) NOT NULL,
    targy      VARCHAR(150) NOT NULL,
    uzenet     TEXT         NOT NULL,
    kuldve     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (kuldve)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Feltöltött képek (galéria)
-- ================================================================
CREATE TABLE kepek (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    feltolto_id     INT UNSIGNED NOT NULL,
    fajlnev         VARCHAR(255) NOT NULL,                   -- public/uploads/ alatti név (generált)
    eredeti_nev     VARCHAR(255) NOT NULL,                   -- a user által megadott név
    leiras          VARCHAR(255) NULL,
    feltoltve       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (feltolto_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Kiadók (lookup tábla a könyvekhez)
-- ================================================================
CREATE TABLE kiadok (
    id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nev     VARCHAR(150) NOT NULL UNIQUE,
    varos   VARCHAR(100) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Szerzők (lookup tábla a könyvekhez)
-- ================================================================
CREATE TABLE szerzok (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    csaladi_nev     VARCHAR(50)  NOT NULL,
    uton_nev        VARCHAR(50)  NOT NULL,
    szuletesi_ev    SMALLINT     NULL,
    nemzetiseg      VARCHAR(60)  NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================================
-- Könyvek — a CRUD modul fő táblája
-- ================================================================
CREATE TABLE konyvek (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cim             VARCHAR(200) NOT NULL,
    szerzo_id       INT UNSIGNED NULL,
    kiado_id        INT UNSIGNED NULL,
    megjelenes_eve  SMALLINT     NULL,
    oldalszam       INT UNSIGNED NULL,
    mufaj           VARCHAR(60)  NULL,
    leiras          TEXT         NULL,
    rogzitve        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (szerzo_id) REFERENCES szerzok(id) ON DELETE SET NULL,
    FOREIGN KEY (kiado_id)  REFERENCES kiadok(id)  ON DELETE SET NULL,
    INDEX (cim),
    INDEX (mufaj)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
