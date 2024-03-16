<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Tickets into minidb (bbcbot)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $users = DB::select('SELECT p.nickname,p.s2_tickets,p.s3_tickets,p.s4_tickets,p.id,p.id FROM players p join points pt on (p.id = pt.player_id)');
 
      $miniDBRaw = array();
      foreach ($users as $user) {
          $miniDBRaw[] = "{$user->nickname} {$user->s2_tickets} {$user->s3_tickets} {$user->s4_tickets} {$user->id} {$user->id}";
      }
      sort($miniDBRaw);
      $miniDB = implode("\n", (array_unique($miniDBRaw)));
      file_put_contents(public_path() . "/exp3/bbcbot/minidb.txt", $miniDB);
      return Command::SUCCESS;
    }
}
