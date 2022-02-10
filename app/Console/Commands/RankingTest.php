<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\PlayerController;
use App\Models\Player;

class RankingTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ranking:test {player : nickname} {--year=0 : 0 = alltime}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ranking test.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $nickname = $this->argument('player');
        $year = $this->option('year');
        $player = Player::where('nickname', $nickname)->first();
        if(!$player){
            echo "Error: player $nickname not found.\n";
            return -1;
        }
        $pc = new PlayerController();
        $stats = $pc->stats($player, $year, 0, true);
        dd(json_encode($stats, JSON_PRETTY_PRINT) . "\n");
        return 0;
    }
}
