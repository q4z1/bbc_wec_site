<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\GameDate;
use App\Models\Player;
use App\Models\Season;

/*
  Step 1: feste Slots (4 pro Tag), immer {days_ahead} Tage im Voraus.
  Step 2/3: dynamisch - pro {holders_per_date} aktive Ticket-Inhaber ein Date pro Woche,
            angelegt am {lead_days}. folgenden Tag, ersetzt dort ein S1 ohne Anmeldungen.
  Step 4: Regel aus dem Admin Manual (erster Freitag 19:30 >= 10 Tage, danach 8-Tage-Rotation).
  Letzter Samstag im Monat: Monthlycup, keine Dates um 19:30 und 21:30.
*/
class CreateGameDates extends Command
{
  protected $signature = 'gamedates:create {--dry-run : Only show what would be created or changed}';

  protected $description = 'Create BBC GameDates (step 1 fixed slots, step 2-4 by ticket holders)';

  // Reihenfolge ist zugleich die S4-Rotation: 19:30 -> 21:30 -> 23:15 -> 01:00
  protected $slots = ['19:30', '21:30', '23:15', '01:00'];

  protected $days_ahead = 21;

  protected $lead_days = 3;

  protected $active_days = 21;

  protected $holders_per_date = 10;

  // S2 laeuft um 19:30/21:30 am besten, S3 um 01:00 kam nie zustande
  protected $slot_preference = [
    2 => ['19:30', '21:30', '23:15', '01:00'],
    3 => ['21:30', '23:15', '19:30'],
  ];

  protected $s4_min_holders = 10;

  protected $s4_min_regs = 10;

  protected $s4_first_min_days = 10;

  protected $s4_retry_days = 8;

  protected $dry = false;

  public function handle()
  {
    $this->dry = (bool) $this->option('dry-run');
    if ($this->dry) $this->warn('DRY RUN - nothing will be written.');

    $season = Season::orderBy('start', 'DESC')->first();
    $this->info("season #{$season->id} started {$season->start}");

    $this->resetStaleDates($season);
    $this->createStepOne();
    foreach ([2, 3] as $step) $this->createDynamic($step, $season);
    $this->createStepFour($season);

    return Command::SUCCESS;
  }

  /**
   * Nach Saisonwechsel sind alle Tickets weg: noch offene S2+ Dates aus der alten Season
   * ohne Anmeldungen werden wieder zu S1 (oder entfernt, falls der Slot schon ein S1 hat).
   */
  private function resetStaleDates(Season $season)
  {
    $stale = GameDate::where('date', '>', Carbon::now())
      ->where('step', '>', 1)
      // updated_at statt created_at: umgewandelte S1 behalten ihr altes created_at
      ->where('updated_at', '<', $season->start)
      ->withCount('regs')
      ->get();

    foreach ($stale as $gd) {
      if ($gd->regs_count > 0) {
        $this->warn("stale step {$gd->step} date {$gd->date} has {$gd->regs_count} registrations - left untouched");
        continue;
      }
      $hasS1 = GameDate::where('date', $gd->date)->where('step', 1)->exists();
      if ($hasS1) {
        $this->line("stale step {$gd->step} date {$gd->date}: removed (slot already has step 1)");
        if (!$this->dry) $gd->delete();
      } else {
        $this->line("stale step {$gd->step} date {$gd->date}: reset to step 1");
        if (!$this->dry) {
          $gd->step = 1;
          $gd->save();
        }
      }
    }
  }

  private function createStepOne()
  {
    $now = Carbon::now();
    $last = GameDate::max('date');
    // wie bisher nur hinter dem letzten Date anhaengen, damit manuell geloeschte Slots nicht wiederkommen
    $day = $last ? Carbon::parse($last)->startOfDay()->addDay() : Carbon::today();
    if ($day->lt(Carbon::today())) $day = Carbon::today();
    $until = Carbon::today()->addDays($this->days_ahead);

    $created = 0;
    for (; $day->lte($until); $day->addDay()) {
      foreach ($this->slots as $time) {
        $dt = $this->slotDateTime($day, $time);
        if ($dt->lte($now) || $this->isMonthlyCup($dt)) continue;
        if (GameDate::where('date', $dt->format('Y-m-d H:i:s'))->exists()) continue;
        if (!$this->dry) GameDate::create(['date' => $dt->format('Y-m-d H:i:s'), 'step' => 1]);
        $created++;
      }
    }
    $this->info("step 1: $created dates created up to " . $until->format('Y-m-d'));
  }

  private function createDynamic(int $step, Season $season)
  {
    $col = "s{$step}_tickets";
    $holders = Player::where($col, '>', 0)->count();
    $active = Player::where($col, '>', 0)
      ->whereExists(function ($q) {
        $q->selectRaw(1)->from('points')
          ->whereColumn('points.player_id', 'players.id')
          ->where('points.game_started', '>=', Carbon::now()->subDays($this->active_days));
      })->count();
    $perWeek = intdiv($active, $this->holders_per_date);

    $target = Carbon::today()->addDays($this->lead_days);
    $windowStart = $target->copy()->subDays(6)->max(Carbon::parse($season->start));
    $inWindow = GameDate::where('step', $step)
      ->whereBetween('date', [$windowStart, $target->copy()->endOfDay()])->count();
    $onTarget = GameDate::where('step', $step)
      ->whereBetween('date', [$target, $target->copy()->endOfDay()])->count();
    // gleichmaessig ueber die Woche verteilen statt alles auf einen Tag
    $perDay = (int) ceil($perWeek / 7);
    $todo = max(0, min($perWeek - $inWindow, $perDay - $onTarget));

    $this->info("step $step: holders $holders, active ($this->active_days d) $active -> $perWeek/week, "
      . "in 7-day window up to " . $target->format('Y-m-d') . ": $inWindow, to create: $todo");

    foreach ($this->slot_preference[$step] as $time) {
      if ($todo < 1) break;
      if ($this->place($this->slotDateTime($target, $time), $step, false)) $todo--;
    }
    if ($todo > 0) $this->warn("step $step: no free slot for $todo more date(s) on " . $target->format('Y-m-d'));
  }

  private function createStepFour(Season $season)
  {
    $now = Carbon::now();
    if (Game::where('type', 4)->where('started', '>=', $season->start)->exists()) {
      $this->info('step 4: already played this season');
      return;
    }

    $last = GameDate::where('step', 4)->where('date', '>=', $season->start)
      ->withCount('regs')->orderBy('date', 'DESC')->first();

    if (!$last) {
      $holders = Player::where('s4_tickets', '>', 0)->count();
      if ($holders < $this->s4_min_holders) {
        $this->info("step 4: $holders holders, need {$this->s4_min_holders}");
        return;
      }
      $earliest = $now->copy()->addDays($this->s4_first_min_days);
      $dt = $earliest->copy()->setTime(19, 30);
      if (!$dt->isFriday() || $dt->lt($earliest)) $dt = $earliest->copy()->next(Carbon::FRIDAY)->setTime(19, 30);
      $this->info("step 4: $holders holders -> first step 4 game");
      $this->place($dt, 4, true);
      return;
    }

    if (Carbon::parse($last->date)->gt($now)) {
      $this->info("step 4: scheduled {$last->date} ({$last->regs_count} registrations)");
      return;
    }
    if ($last->regs_count >= $this->s4_min_regs) {
      $this->info("step 4: {$last->date} had {$last->regs_count} registrations - waiting for the result upload");
      return;
    }
    // einen Tag Puffer, falls das Spiel mit Einverstaendnis doch lief und das Ergebnis noch fehlt
    if (Carbon::parse($last->date)->addDay()->gt($now)) {
      $this->info("step 4: {$last->date} had only {$last->regs_count} registrations - waiting a day before rescheduling");
      return;
    }

    $dt = $this->nextS4Rotation(Carbon::parse($last->date));
    while ($dt->lte($now)) $dt = $this->nextS4Rotation($dt);
    $this->info("step 4: {$last->date} had only {$last->regs_count} registrations -> rescheduling");
    $this->place($dt, 4, true);
  }

  // +8 Tage, naechste regulaere Uhrzeit: Fr 19:30 -> Sa 21:30 -> So 23:15 -> Mo 01:00 -> Di 19:30 ...
  private function nextS4Rotation(Carbon $from)
  {
    $idx = array_search($from->format('H:i'), $this->slots);
    $time = $this->slots[$idx === false ? 0 : ($idx + 1) % count($this->slots)];
    $dt = $this->slotDateTime($from->copy()->addDays($this->s4_retry_days), $time);
    // Monthlycup: auf die naechste freie Uhrzeit desselben Tages ausweichen
    while ($this->isMonthlyCup($dt)) {
      $dt = $this->slotDateTime($dt, $this->slots[(array_search($dt->format('H:i'), $this->slots) + 1) % count($this->slots)]);
    }
    return $dt;
  }

  /**
   * Legt ein Date im Slot an: ein S1 ohne Anmeldungen wird ersetzt. Ist das S1 schon belegt,
   * wird S2/S3 uebersprungen, S4 (fester Termin) kommt zusaetzlich dazu.
   */
  private function place(Carbon $dt, int $step, bool $alongside)
  {
    $date = $dt->format('Y-m-d H:i:s');
    if ($dt->lte(Carbon::now()) || $this->isMonthlyCup($dt)) return false;

    $existing = GameDate::where('date', $date)->withCount('regs')->get();
    if ($existing->where('step', '>', 1)->isNotEmpty()) {
      $this->line("  $date: already has step " . $existing->max('step') . " - skipped");
      return false;
    }

    $s1 = $existing->firstWhere('step', 1);
    if ($s1 && $s1->regs_count == 0) {
      $this->line("  $date: step 1 -> step $step");
      if (!$this->dry) {
        $s1->step = $step;
        $s1->save();
      }
      return true;
    }
    if ($s1 && !$alongside) {
      $this->line("  $date: step 1 has {$s1->regs_count} registrations - skipped");
      return false;
    }

    $this->line("  $date: step $step created" . ($s1 ? " (alongside step 1 with {$s1->regs_count} registrations)" : ''));
    if (!$this->dry) GameDate::create(['date' => $date, 'step' => $step]);
    return true;
  }

  private function slotDateTime(Carbon $day, string $time)
  {
    [$h, $m] = explode(':', $time);
    return $day->copy()->setTime((int) $h, (int) $m);
  }

  // letzter Samstag im Monat, 19:30 und 21:30
  private function isMonthlyCup(Carbon $dt)
  {
    return $dt->isSaturday()
      && in_array($dt->format('H:i'), ['19:30', '21:30'])
      && $dt->copy()->addWeek()->month !== $dt->month;
  }
}
