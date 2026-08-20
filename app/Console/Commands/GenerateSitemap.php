<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\Page;
use App\Models\Player;
use Illuminate\Console\Command;

/**
 * Writes public/sitemap.xml. The set mirrors the "bewusst offen" list in
 * public/robots.txt: anything behind the auth middleware would only answer a
 * crawler with a redirect to /login.
 *
 * Built as a command rather than a route because the file holds ~10.000 URLs;
 * the schedule lives in the pthranking application, which is driven by cron.
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
        $add(route('player.all'), 'weekly', '0.7');
        $add(route('registration'), 'daily', '0.7');
        $add(route('shoutbox'), 'daily', '0.6');

        foreach (Page::where('active', 1)->where('slug', '!=', 'home')->get() as $page) {
            $add(url('/page/'.$page->slug), 'monthly', '0.6', $page->updated_at);
        }

        foreach (Player::orderBy('id')->cursor() as $player) {
            $add(route('player', $player->id), 'weekly', '0.5', $player->updated_at);
        }

        foreach (Game::orderByDesc('started')->cursor() as $game) {
            $add(route('results.game', $game->id), 'yearly', '0.3', $game->updated_at);
        }

        return $urls;
    }
}
