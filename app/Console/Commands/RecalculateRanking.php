<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Http\Controllers\SeasonController;
use App\Models\Game;
use App\Models\Point;
use App\Models\Season;

/*
  Die Ranking-Seite rechnet ausschliesslich ueber die points-Tabelle. Beim Upload
  bzw. beim Bearbeiten eines Spiels werden Startzeit, Typ und Platzierungen aber
  zusaetzlich in games geschrieben - wer dort etwas korrigiert (z.B. die Startzeit),
  laesst die points-Zeilen stehen und das Spiel faellt aus der Wertung.
  Dieser Befehl macht games wieder zur Quelle der Wahrheit und leert den Cache.
  Default ist die laufende Season - historische Seasons nur bewusst mit --all.
*/
class RecalculateRanking extends Command
{
  protected $signature = 'ranking:recalculate
                          {--game= : Nur dieses Spiel (games.number), unabhaengig von der Season}
                          {--season= : Ab dieser Season rechnen (Default: die laufende)}
                          {--all : Alle Spiele, auch die historischen Seasons}
                          {--dry-run : Nur zeigen, was sich aendern wuerde}';

  protected $description = 'Rebuild the points table from the games table and flush the ranking cache';

  // Platz 1-10, multipliziert mit dem Step
  protected $points = [10, 9, 8, 7, 6, 5, 4, 3, 2, 1];

  protected $dry = false;

  public function handle()
  {
    $this->dry = (bool) $this->option('dry-run');
    if ($this->dry) $this->warn('DRY RUN - nothing will be written.');

    $games = Game::orderBy('number', 'ASC');
    if ($number = $this->option('game')) {
      $games = $games->where('number', $number);
      if (!$games->clone()->exists()) {
        $this->error("game #{$number} not found");
        return Command::FAILURE;
      }
    } elseif ($this->option('all')) {
      $this->warn('rechne ueber ALLE Seasons - historische Rankings koennen sich aendern.');
    } else {
      // Historische Seasons bleiben unangetastet: erst ab dem Start der gewaehlten Season.
      $season = $this->option('season') ?: Season::orderBy('start', 'DESC')->first()->id;
      if (!Season::where('id', $season)->exists()) {
        $this->error("season #{$season} not found");
        return Command::FAILURE;
      }
      $start = SeasonController::dateRange($season)['start'];
      $games = $games->where('started', '>=', $start);
      $this->info("ab season #{$season} ({$start})");
    }

    $touched = $created = $updated = $deleted = 0;

    $games->chunk(200, function ($chunk) use (&$touched, &$created, &$updated, &$deleted) {
      foreach ($chunk as $game) {
        $changes = $this->syncGame($game);
        $created += $changes['created'];
        $updated += $changes['updated'];
        $deleted += $changes['deleted'];
        if (array_sum($changes)) $touched++;
      }
    });

    $this->info("games with changes: {$touched} (created {$created}, updated {$updated}, deleted {$deleted})");

    if ($this->dry) return Command::SUCCESS;

    Cache::flush();
    $this->info('cache flushed');

    return Command::SUCCESS;
  }

  /**
   * Bringt die points-Zeilen eines Spiels mit der games-Zeile in Deckung.
   */
  protected function syncGame(Game $game): array
  {
    $started = Carbon::parse($game->started)->format('Y-m-d H:i:s');
    $changes = ['created' => 0, 'updated' => 0, 'deleted' => 0];

    $existing = Point::where('game_id', $game->id)->orderBy('id', 'ASC')->get()->groupBy('pos');

    for ($i = 1; $i <= 10; $i++) {
      $player_id = (int) $game->{"pos$i"};
      $rows = $existing->get($i, collect());

      if (!$player_id) {
        // unbesetzter Platz - darf keine Punkte haben
        foreach ($rows as $row) {
          $this->line("game #{$game->number} pos{$i}: delete point {$row->id} (no player)");
          if (!$this->dry) $row->delete();
          $changes['deleted']++;
        }
        continue;
      }

      $expected = [
        'player_id' => $player_id,
        'type' => (int) $game->type,
        'points' => $this->points[$i - 1] * (int) $game->type,
        'game_started' => $started,
      ];

      $row = $rows->shift();
      if (!$row) {
        $this->line("game #{$game->number} pos{$i}: create point for player {$player_id}");
        if (!$this->dry) {
          $point = new Point();
          $point->game_id = $game->id;
          $point->pos = $i;
          foreach ($expected as $key => $value) $point->{$key} = $value;
          $point->save();
        }
        $changes['created']++;
      } else {
        $diff = [];
        foreach ($expected as $key => $value) {
          $current = ($key == 'game_started')
            ? Carbon::parse($row->{$key})->format('Y-m-d H:i:s')
            : (int) $row->{$key};
          if ($current != $value) $diff[$key] = "{$current} -> {$value}";
        }
        if ($diff) {
          $this->line("game #{$game->number} pos{$i}: point {$row->id} " . json_encode($diff));
          if (!$this->dry) {
            foreach ($expected as $key => $value) $row->{$key} = $value;
            $row->save();
          }
          $changes['updated']++;
        }
      }

      // Duplikate auf demselben Platz
      foreach ($rows as $dupe) {
        $this->line("game #{$game->number} pos{$i}: delete duplicate point {$dupe->id}");
        if (!$this->dry) $dupe->delete();
        $changes['deleted']++;
      }
    }

    // Zeilen mit einem Platz ausserhalb 1-10
    foreach ($existing as $pos => $rows) {
      if ($pos >= 1 && $pos <= 10) continue;
      foreach ($rows as $row) {
        $this->line("game #{$game->number}: delete point {$row->id} (pos {$pos})");
        if (!$this->dry) $row->delete();
        $changes['deleted']++;
      }
    }

    return $changes;
  }
}
