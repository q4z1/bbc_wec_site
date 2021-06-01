<?php

namespace App\Http\Controllers;

use App\Models\GameDate;
use App\Models\User;
use Illuminate\Http\Request;

class GameDateController extends Controller
{
    public function get(GameDate $date){
        $gd = GameDate::where('id', $date->id)->with('regs.player')->first();
        $admin = false;
        foreach($gd->regs as $i => $reg){
            $p = $reg->player;
            $u = User::where('name', $p->nickname)->first();
            if($p && $u && in_array($u->role, ['a', 's'])){
                if(!$admin){
                    $gd->regs[$i]->player->admin = true;
                    $admin = true;
                }else{
                    $gd->regs[$i]->player->admin = false;
                }
            }else{
                $gd->regs[$i]->player->admin = false;
            }
        }
        return  ['success' => true, 'date' => $gd];
    }

    public function add(Request $request)
    {
        if(!(auth() && in_array(auth()->user()->role, ['a', 's']))) return ['success' => false, 'msg' => 'Not valid.'];
        $date = $request->input('date', null);
        $step = $request->input('step', null);
        if(!$date || !$step) return ['success' => false, 'msg' => 'Missing paramter(s).'];
        $d = GameDate::where([['date', $date],['step', $step]])->first();
        if($d) return ['success' => false, 'msg' => 'Game already exists.'];
        elseif(strtotime($date) < strtotime("now")) return ['success' => false, 'msg' => 'Date/Time is in the past.'];
        $d = new GameDate();
        $d->date = $date;
        $d->step = $step;
        $d->save();
        return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
    }

    public function update(Request $request, $gameDate)
    {
        if(!(auth() && in_array(auth()->user()->role, ['s']))) return ['success' => false, 'msg' => 'Not valid.'];
        $date = $request->input('date', null);
        $step = $request->input('step', null);
        if(!$date || !$step) return ['success' => false, 'msg' => 'Missing paramter(s).'];
        $d = GameDate::where([['date', $date],['step', $step]])->first();
        if($d) return ['success' => false, 'msg' => 'Game already exists.'];
        elseif(strtotime($date) < strtotime("now")) return ['success' => false, 'msg' => 'Date/Time is in the past.'];
        $gameDate->step = $step;
        $gameDate->date = $date;
        $gameDate->save();
        return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
    }

    public function delete(GameDate $date)
    {
        if(!(auth() && in_array(auth()->user()->role, ['s']))) return ['success' => false, 'msg' => 'Not valid.'];
        GameDate::where('id', $date->id)->first()->delete();
        return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
    }

}