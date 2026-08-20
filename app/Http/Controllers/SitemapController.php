<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Page;
use App\Models\Player;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Sitemap of everything a guest can actually reach. The set mirrors the
 * "bewusst offen" list in public/robots.txt: pages behind the auth middleware
 * would only answer a crawler with a redirect to /login.
 */
class SitemapController extends Controller
{
    /** Rebuilding walks ~10.000 rows, so the result is cached. */
    private const CACHE_MINUTES = 360;

    public function index(): Response
    {
        $xml = Cache::remember('sitemap.xml', now()->addMinutes(self::CACHE_MINUTES), function () {
            return view('sitemap', ['urls' => $this->urls()])->render();
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
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
