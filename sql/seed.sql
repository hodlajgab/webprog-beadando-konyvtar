-- ================================================================
-- Mintaadatok az alkalmazás kipróbálásához
-- ================================================================

-- Kiadók
INSERT INTO kiadok (nev, varos) VALUES
    ('Európa Könyvkiadó',    'Budapest'),
    ('Magvető Kiadó',        'Budapest'),
    ('Helikon Kiadó',        'Budapest'),
    ('Park Könyvkiadó',      'Budapest'),
    ('Athenaeum Kiadó',      'Budapest');

-- Szerzők
INSERT INTO szerzok (csaladi_nev, uton_nev, szuletesi_ev, nemzetiseg) VALUES
    ('Tolkien',     'J. R. R.',  1892, 'angol'),
    ('Rowling',     'J. K.',     1965, 'angol'),
    ('Orwell',      'George',    1903, 'angol'),
    ('Murakami',    'Haruki',    1949, 'japán'),
    ('Szabó',       'Magda',     1917, 'magyar'),
    ('Esterházy',   'Péter',     1950, 'magyar'),
    ('Krasznahorkai', 'László',  1954, 'magyar'),
    ('García Márquez', 'Gabriel', 1927, 'kolumbiai');

-- Könyvek
INSERT INTO konyvek (cim, szerzo_id, kiado_id, megjelenes_eve, oldalszam, mufaj, leiras) VALUES
    ('A Gyűrűk Ura',                 1, 1, 1954, 1216, 'fantasy',
     'Tolkien klasszikus epikus fantasy regénye Középföldéről.'),
    ('A hobbit',                      1, 1, 1937,  336, 'fantasy',
     'Bilbó Baggins kalandos utazása a Magányos-hegyhez.'),
    ('Harry Potter és a bölcsek köve', 2, 4, 1997,  336, 'fantasy',
     'Egy fiatal varázsló első éve a Roxfortban.'),
    ('1984',                           3, 3, 1949,  328, 'disztópia',
     'Orwell sötét jövőképe a totalitárius állam mindennapjairól.'),
    ('Az állatok forradalma',          3, 3, 1945,  144, 'szatíra',
     'Politikai szatíra állatszereplőkkel.'),
    ('Norvég erdő',                    4, 2, 1987,  296, 'szépirodalom',
     'Murakami melankolikus fiatalkor-regénye.'),
    ('Az ajtó',                        5, 2, 1987,  264, 'szépirodalom',
     'Szabó Magda önéletrajzi ihletésű regénye Emerencről.'),
    ('Hahn-Hahn grófnő pillantása',    6, 2, 1991,  240, 'szépirodalom',
     'Esterházy Péter jellegzetes posztmodern stílusban.'),
    ('Sátántangó',                     7, 2, 1985,  336, 'szépirodalom',
     'Krasznahorkai László nagyszabású regénye.'),
    ('Száz év magány',                 8, 1, 1967,  448, 'mágikus realizmus',
     'García Márquez családregénye Macondóról.');

-- Megjegyzés: a users táblát üresen hagyjuk, regisztrációval töltődik fel.
