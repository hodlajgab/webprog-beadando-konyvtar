<?php
declare(strict_types=1);

namespace Controllers;

final class FoooldalController extends Controller
{
    public function mutat(): void
    {
        // A főoldalon nincs külön DB lekérdezés, csak a téma bemutatása.
        $this->nezet('fooldal', [], 'Főoldal — Könyvtár');
    }
}
