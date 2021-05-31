<?php

namespace App\Http\Controllers;

use App\Models\GameDate;
use App\Models\Player;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

    public function index(Request $request)
    {
        return view('registration', [
            'gamedates' => GameDate::getUpcomingGames(), 'calstart' => date("Y-m-01", strtotime('-1 month'))]);
    }

    public function update(Request $request, Registration $reg)
    {
        //
    }

    public function delete(Registration $reg)
    {
        if(!(auth() && in_array(auth()->user()->role, ['s']))) return ['success' => false, 'msg' => 'Not valid.'];
        Registration::where('id', $reg->id)->first()->delete();
        return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
    }

    public function register(Request $request, GameDate $date)
    {
        $nickname = $request->input('nickname', "");
        if($nickname === "") return ['success' => false, 'msg' => 'Username empty!'];
        if(strtotime("-15 minute") > strtotime($date->date)) return ['success' => false, 'msg' => 'Registration too late!'];
        $p = Player::where('nickname', $nickname)->first();
        if(!$p){
            $p = new Player();
            $p->nickname = $nickname;
            $p->new = 1;
            $p->save();
        }
        if(Registration::where([['game_date_id', $date->id],['player_id', $p->id]])->first()) return ['success' => false, 'msg' => 'Already registered!'];
        $reg = new Registration();
        $reg->game_date_id = $date->id;
        $reg->player_id = $p->id;
        $reg->fp = $request->input('fp', '');
        $reg->ip = $request->ip();
        $reg->save();
        return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
    }

}
