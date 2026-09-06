<?php

namespace App\Console\Commands;

use App\Models\Player;
use Illuminate\Console\Command;

/**
 * Writes public/sitemap.xml. The set mirrors the "bewusst offen" list in
 * public/robots.txt; everything else answers a crawler with a redirect to
 * /login. The schedule lives in the pthranking application, which is the one
 * driven by cron.
 */
class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate
                            {--output= : target file, defaults to public/sitemap.xml}
                            {--dry-run : only count, write nothing}';

    protected $description = 'Generate sitemap.xml';

    public function handle(): int
    {
        $urls = $this->urls();
        $this->line(count($urls).' URLs');

        if ($this->option('dry-run')) {
            $this->line('dry run, nothing written');

            return self::SUCCESS;
        }

        $target = $this->option('output') ?: public_path('sitemap.xml');
        $xml = view('sitemap', ['urls' => $urls])->render();

        // The scheduler runs as root, interactive work as devuser. Once cron has
        // written the file it is owned by root and file_put_contents would fail;
        // unlinking first works because the directory belongs to devuser.
        if (is_file($target) && ! is_writable($target)) {
            @unlink($target);
        }

        if (file_put_contents($target, $xml) === false) {
            $this->error("could not write $target");

            return self::FAILURE;
        }

        $this->info($target.' written ('.number_format(strlen($xml) / 1024, 1).' KB)');

        return self::SUCCESS;
    }

    private function urls(): array
    {
        $urls = [];
        $add = function (string $loc, string $changefreq, string $priority, $lastmod = null) use (&$urls) {
            $urls[] = [
                'loc' => $loc,
                'changefreq' => $changefreq,
                'priority' => $priority,
                'lastmod' => $lastmod?->toAtomString(),
            ];
        };

        $add(url('/'), 'daily', '1.0');
        $add(route('results'), 'weekly', '0.9');
        $add(route('results.ranking'), 'weekly', '0.9');
        $add(route('results.halloffame'), 'weekly', '0.8');
        $add(route('players'), 'weekly', '0.7');
        $add(route('shoutbox'), 'daily', '0.6');

        // Die player-Route bindet ueber den Nickname, nicht die id.
        foreach (Player::orderBy('id')->cursor() as $player) {
            $add(route('player', $player->nickname), 'weekly', '0.5', $player->updated_at);
        }

        // Die einzelnen Spielseiten tragen meta robots noindex (siehe
        // resources/views/game.blade.php) und gehoeren deshalb nicht in die
        // Sitemap. Erreichbar bleiben sie ueber die Ergebnisliste.

        return $urls;
    }
}
