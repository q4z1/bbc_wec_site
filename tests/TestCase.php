<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Reissleine: die Suite darf niemals die Live-Datenbank anfassen.
     *
     * phpunit.xml zeigt auf In-Memory-SQLite und setzt APP_CONFIG_CACHE auf
     * einen nicht existierenden Pfad, damit ein produktiver Config-Cache diese
     * Werte nicht ueberschreiben kann. Diese Pruefung faengt den Fall ab, dass
     * doch einmal etwas anderes durchkommt.
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        $default = config('database.default');
        $connection = config("database.connections.$default");

        if (($connection['driver'] ?? null) !== 'sqlite' || ($connection['database'] ?? null) !== ':memory:') {
            throw new RuntimeException(sprintf(
                'Refusing to run the test suite against "%s" (%s). Tests require in-memory SQLite.',
                $default,
                $connection['database'] ?? 'unknown'
            ));
        }

        return $app;
    }
}
