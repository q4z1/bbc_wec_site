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
            $admin = false;
            foreach($date->regs as $i => $reg){
                $p = $reg->player;
                $u = User::where('name', $p->nickname)->first();
                if($p && $u && in_array($u->role, ['a', 's'])){
                    if(!$admin){
                        $date->regs[$i]->player->admin = true;
                        $admin = true;
                    }else{
                        $date->regs[$i]->player->admin = false;
                    }
                }else{
                    $date->regs[$i]->player->admin = false;
                }
            }
            return $date;
        });
        return $dates;
    }
}
