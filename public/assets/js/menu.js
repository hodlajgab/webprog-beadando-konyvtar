// Hamburger-menü kezelése mobil nézeten
(function () {
    'use strict';

    const hamburger = document.querySelector('.hamburger');
    const menu = document.getElementById('fomenu');

    if (!hamburger || !menu) {
        return;
    }

    hamburger.addEventListener('click', function () {
        const nyitva = menu.classList.toggle('nyitva');
        hamburger.setAttribute('aria-expanded', nyitva ? 'true' : 'false');
    });
})();
