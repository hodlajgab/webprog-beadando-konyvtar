// Kapcsolat-űrlap kliens-oldali validáció.
// Megjegyzés: a HTML-ben szándékosan nincs `required`/`pattern` —
// minden ellenőrzés itt és a szerveren történik.
(function () {
    'use strict';

    const urlap = document.getElementById('kapcsolatUrlap');
    if (!urlap) {
        return;
    }

    const mezok = {
        nev:    document.getElementById('nev'),
        email:  document.getElementById('email'),
        targy:  document.getElementById('targy'),
        uzenet: document.getElementById('uzenet'),
    };

    const hibaCimkek = {
        nev:    document.getElementById('hiba_nev'),
        email:  document.getElementById('hiba_email'),
        targy:  document.getElementById('hiba_targy'),
        uzenet: document.getElementById('hiba_uzenet'),
    };

    function ervenyesEmail(ertek) {
        // Egyszerű, böngésző-független email regex (a szerver a végső szűrő)
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(ertek);
    }

    function validal() {
        const hibak = {};

        const nev = mezok.nev.value.trim();
        if (nev.length < 2) {
            hibak.nev = 'A név legalább 2 karakter legyen.';
        } else if (nev.length > 100) {
            hibak.nev = 'A név legfeljebb 100 karakter lehet.';
        }

        const email = mezok.email.value.trim();
        if (email === '') {
            hibak.email = 'Az e-mail cím megadása kötelező.';
        } else if (!ervenyesEmail(email)) {
            hibak.email = 'Érvényes e-mail címet adj meg.';
        }

        const targy = mezok.targy.value.trim();
        if (targy.length < 3) {
            hibak.targy = 'A tárgy legalább 3 karakter legyen.';
        } else if (targy.length > 150) {
            hibak.targy = 'A tárgy legfeljebb 150 karakter lehet.';
        }

        const uzenet = mezok.uzenet.value.trim();
        if (uzenet.length < 10) {
            hibak.uzenet = 'Az üzenet legalább 10 karakteres legyen.';
        } else if (uzenet.length > 5000) {
            hibak.uzenet = 'Az üzenet legfeljebb 5000 karakter lehet.';
        }

        return hibak;
    }

    function hibakKiir(hibak) {
        Object.keys(hibaCimkek).forEach(function (kulcs) {
            hibaCimkek[kulcs].textContent = hibak[kulcs] || '';
        });
    }

    // Élő visszajelzés blur-on
    Object.keys(mezok).forEach(function (kulcs) {
        mezok[kulcs].addEventListener('blur', function () {
            const hibak = validal();
            hibaCimkek[kulcs].textContent = hibak[kulcs] || '';
        });
    });

    urlap.addEventListener('submit', function (esemeny) {
        const hibak = validal();
        hibakKiir(hibak);

        if (Object.keys(hibak).length > 0) {
            esemeny.preventDefault();
            // Az első hibás mezőre fókuszáljon
            const elsoHibasKulcs = Object.keys(hibak)[0];
            if (mezok[elsoHibasKulcs]) {
                mezok[elsoHibasKulcs].focus();
            }
        }
    });
})();
