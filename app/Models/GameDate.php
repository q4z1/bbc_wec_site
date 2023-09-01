<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameDate extends Model
{
    use HasFactory;

    public function regs(){
        return $this->hasMany(Registration::class, 'game_date_id', 'id');
    }

    public static function getUpcomingGames(){
        $dates = self::where('date','>=', date("Y-m-01 00:00:00", strtotime('-1 month')))->with('regs.player')->get()->map(function($date){
            $date->num = count($date->regs);
            unset($date->regs);
            return $date;
        });
        return $dates;
    }

    public static function getNextGamesByStep($step){
      $dates = self::where('date','>=', date("Y-m-d H:i:s"))->where('step', '=', $step)->with('regs.player')->get();
      return $dates;
  }
}
