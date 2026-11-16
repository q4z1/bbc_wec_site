<?php

namespace App\Console\Commands;

use App\Models\Action;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/*
  Vergibt die Meilenstein-Awards "1000 Games", "2000 Games" und "3000 Games".
  Gezaehlt werden alle BBC-Spiele (alle Seasons, alle Steps) = points-Zeilen des
  Spielers, genau wie "games" in der Alltime-Statistik der Spielerseite.

  Nur additiv: es wird nie ein Award entfernt, und wer einen Award (oder eine
  der unten gelisteten Sonderversionen) schon hat, bekommt ihn nicht nochmal.
  Angestossen taeglich vom Scheduler in /var/www/pokerth/pthranking.
*/
class AssignGameAwards extends Command
{
  protected $signature = 'awards:games {--dry-run : Only show what would be assigned}';

  protected $description = 'Assign the 1000/2000/3000 Games awards to players who reached the milestone';

  // Spiele => award_id
  protected $milestones = [
    1000 => 120, // 1000 Games
    2000 => 121, // 2000 Games
    3000 => 127, // 3000 Games
  ];

  // Sonderversionen, die einen Meilenstein-Award ersetzen (award_id => award_id).
  // Wer eine davon hat, bekommt den regulaeren Award nicht zusaetzlich.
  protected $replacements = [];

  // actions.user ist NOT NULL - 0 = automatische Vergabe (Anzeige als "system")
  const SYSTEM_USER = 0;

  public function handle()
  {
    $dry = (bool) $this->option('dry-run');
    if ($dry) $this->warn('DRY RUN - nothing will be written.');

    $titles = DB::table('awards')->whereIn('id', $this->milestones)->pluck('title', 'id');
    foreach ($this->milestones as $awardId) {
      if (!isset($titles[$awardId])) {
        $this->error("award #{$awardId} not found");
        return Command::FAILURE;
      }
    }

    $players = DB::table('points')
      ->join('players', 'players.id', '=', 'points.player_id')
      ->select('players.id', 'players.nickname', DB::raw('COUNT(*) AS games'))
      ->groupBy('players.id', 'players.nickname')
      ->having('games', '>=', min(array_keys($this->milestones)))
      ->get();

    $held = DB::table('player_awards')
      ->whereIn('player_id', $players->pluck('id'))
      ->whereIn('award_id', array_merge($this->milestones, array_keys($this->replacements)))
      ->get()
      ->groupBy('player_id')
      ->map(fn ($rows) => $rows->pluck('award_id')
        ->map(fn ($id) => $this->replacements[$id] ?? $id)
        ->all());

    $assigned = 0;
    foreach ($players as $player) {
      foreach ($this->milestones as $games => $awardId) {
        if ($player->games < $games) continue;
        if (in_array($awardId, $held[$player->id] ?? [])) continue;

        $this->line("{$titles[$awardId]} -> {$player->nickname} ({$player->games} games)");
        $assigned++;
        if ($dry) continue;

        DB::transaction(function () use ($player, $awardId, $titles) {
          DB::table('player_awards')->insert([
            'player_id' => $player->id,
            'award_id' => $awardId,
            'created_at' => now(),
            'updated_at' => now(),
          ]);
          $action = new Action();
          $action->action = "Award {$titles[$awardId]} assigned to {$player->nickname}.";
          $action->reason = "auto: {$player->games} games";
          $action->user = self::SYSTEM_USER;
          $action->save();
        });
      }
    }

    $this->info("assigned: {$assigned}");
    return Command::SUCCESS;
  }
}
