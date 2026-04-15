<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\GameDate;
use App\Models\Season;
use App\Http\Controllers\SeasonController;

class CreateGameDates extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'gamedates:create';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create BBC GameDates';

  protected $matrix_file = 'private/gamedate_matrix.jconf';

  protected $diff_days_limit = 21;

  protected $matrix_file_comments = "/*" . PHP_EOL
    . "\t" . "This file is a matrix for automated gamedate creation ..." . PHP_EOL
    . "\t" . "It defines the step games for each day of the week in a 2-week cycle (odd or even weeknumber)." . PHP_EOL
    . "\t" . "Step games higher than 2 will be automatically created in respect to the amount of player tickets, replacing a step 2 game." . PHP_EOL
    . "\t" . PHP_EOL
    . "\t" . "Keep all Brackets and other characters intact, as they are required for parsing." . PHP_EOL
    . "*/" . PHP_EOL;

  protected $matrix = null;

  protected $default_matrix = [
    "week_even" => [
      "mon" => [
        "1am" => 2,
        "19:30" => 1,
        "21:30" => 1,
        "23:15" => 1
      ],
      "tue" => [
        "1am" => 1,
        "19:30" => 2,
        "21:30" => 1,
        "23:15" => 1
      ],
      "wed" => [
        "1am" => 1,
        "19:30" => 1,
        "21:30" => 2,
        "23:15" => 1
      ],
      "thu" => [
        "1am" => 1,
        "19:30" => 1,
        "21:30" => 1,
        "23:15" => 2
      ],
      "fri" => [
        "1am" => 2,
        "19:30" => 1,
        "21:30" => 1,
        "23:15" => 1
      ],
      "sat" => [
        "1am" => 1,
        "19:30" => 2,
        "21:30" => 1,
        "23:15" => 1
      ],
      "sun" => [
        "1am" => 1,
        "19:30" => 1,
        "21:30" => 2,
        "23:15" => 1
      ],
    ],
    "week_odd" => [
      "mon" => [
        "1am" => 1,
        "19:30" => 1,
        "21:30" => 1,
        "23:15" => 2
      ],
      "tue" => [
        "1am" => 2,
        "19:30" => 1,
        "21:30" => 1,
        "23:15" => 1
      ],
      "wed" => [
        "1am" => 1,
        "19:30" => 2,
        "21:30" => 1,
        "23:15" => 1
      ],
      "thu" => [
        "1am" => 1,
        "19:30" => 1,
        "21:30" => 2,
        "23:15" => 1
      ],
      "fri" => [
        "1am" => 1,
        "19:30" => 1,
        "21:30" => 1,
        "23:15" => 2
      ],
      "sat" => [
        "1am" => 2,
        "19:30" => 1,
        "21:30" => 1,
        "23:15" => 1
      ],
      "sun" => [
        "1am" => 1,
        "19:30" => 2,
        "21:30" => 1,
        "23:15" => 1
      ],
    ]
  ];

  private function matrix_file_init()
  {
    $this->matrix = json_decode(Storage::disk('local')->get($this->matrix_file));
  }

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    $this->matrix_file_init();

    $s2 = DB::table('players')->max('s2_tickets');
    $s3 = DB::table('players')->max('s3_tickets');
    $s4 = DB::table('players')->max('s4_tickets');

    $season = Season::orderBy('start', 'DESC')->first()->id;
    $sr = SeasonController::dateRange($season);

    $season_start = $sr['start'];
    $season_end = $sr['end'];

    // Anzahl Tage seit Saisonbeginn und abgerundete Wochen (ganzzahlig)
    $days_since_season_start = Carbon::parse($season_start)->diffInDays(Carbon::now());
    $weeks_since_season_start = intdiv($days_since_season_start, 7);

    echo "season start: $season_start, season end: $season_end\n";
    echo "days since season start: $days_since_season_start, weeks (floored): $weeks_since_season_start\n";
    

    $last = GameDate::latest('date')->first();

    $diff_days_from_now = Carbon::parse($last->date)->diffInDays(Carbon::now());
    $weekdayLast = strtolower(Carbon::parse($last->date)->format('D'));
    $weekNumLast = Carbon::parse($last->date)->format("W");
    $weekEvenLast = $weekNumLast % 2 === 0;

    echo "last created GameDate: " . $last->date . " - step: " . $last->step . "\n";
    echo "weekday last: $weekdayLast, week number last: $weekNumLast , week number last even: " . ($weekEvenLast ? 'true' : 'false') . "\n";
    echo "days until last created GameDate: $diff_days_from_now, limit of days until last created GameDate: {$this->diff_days_limit}\n";
    echo "current tickets: S2=$s2, S3=$s3, S4=$s4\n";

    /*
      if S2 amont < 30, run jconf_1 (4 S1 /day)
      if S2 amount > 30, run jconf_2 (4 S1 /day and + S2 /day)
      if S3 amount > 20, run jconf_3 (4 S1 /day + one S2 /day + one S3 /week)
      if S4 amount = or > 12, run jconf_4 (4 S1 /day + 2 S2/ day + 2 S3 /week + one S4)
      if last saturday of the month, run jconf_5 (3 S1 in the day)
    */

    if ($diff_days_from_now >= $this->diff_days_limit) {
      $this->info("Last created GameDate is within the limit of {$this->diff_days_limit} days. No new GameDates created.");
      return Command::SUCCESS;
    }
    $this->info("Last created GameDate is below the limit of {$this->diff_days_limit} days. Creating new GameDates...");
    for($i =1; $i <= ($this->diff_days_limit - $diff_days_from_now); $i++) {
      $date = Carbon::parse($last->date)->addDays($i);
      $weekNum = $date->format("W");
      $weekDay = strtolower($date->format('D'));
      
      // for 2 weeks matrix
      // $weekEven = $weekNum % 2 === 0; 
      // $matrix_week = $weekEven ? 'week_even' : 'week_odd'; 
      
      // for 4 weeks matrix
      $matrix_week = (($weekNum - 1) % 4) + 1; // for 4 weeks matrix

      foreach ($this->matrix->$matrix_week->$weekDay as $gdO) {
        $this->info("Creating GameDate for ".$date->format('Y-m-d')." $gdO->time with step $gdO->step");
        $gd = new GameDate();
        $gd->date = $date->format('Y-m-d') . ' ' . $gdO->time;
        $gd->step = $gdO->step;
        $gd->save();
      }
    }
    $this->info(($this->diff_days_limit - $diff_days_from_now) . " days with GameDates created successfully.");

    return Command::SUCCESS;
  }
}
