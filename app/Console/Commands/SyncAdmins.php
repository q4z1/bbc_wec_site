<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SyncAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admins:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync BBC admins into bbcadmins.txt (bbcbot)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $admins = User::whereIn('role', ['a', 's'])->pluck('name')->unique()->sort()->values()->all();
      file_put_contents(public_path() . "/exp3/bbcbot/bbcadmins.txt", implode("\n", $admins));
      return Command::SUCCESS;
    }
}
