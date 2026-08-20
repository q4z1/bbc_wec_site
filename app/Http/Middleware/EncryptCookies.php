<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except = [
        // Der Theme-Umschalter schreibt dieses Cookie aus JavaScript.
        // Verschluesselt wuerde Laravel den Wert beim naechsten Request
        // verwerfen und das Theme fiele jedes Mal zurueck.
        'theme',
    ];
}
