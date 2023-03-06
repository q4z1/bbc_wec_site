<?php

namespace App\Http\Controllers;

use App\Models\GameDate;
use App\Models\Player;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth', ['only' => ['delete', 'update']]);
  }
  public function index(Request $request)
  {
    return view('registration', [
      'gamedates' => GameDate::getUpcomingGames(), 'calstart' => date("Y-m-01", strtotime('-1 month'))
    ]);
  }

  public function update(Request $request, Registration $reg)
  {
    //
  }

  public function delete(Registration $reg)
  {
    $reg = Registration::where('id', $reg->id)->first();
    $gd = GameDate::where('id', $reg->game_date_id)->first();
    $p = Player::where('id', $reg->player_id)->first();
    $u = User::where('name', $p->nickname)->first();
    if (!(in_array(auth()->user()->role, ['s'])) && !($u && $u->id == auth()->id() && time() < (strtotime($gd->date) - 60 * 60))) return ['success' => false, 'msg' => 'Not valid.'];
    $reg->delete();
    return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
  }

  public function register(Request $request, GameDate $date)
  {
    $nickname = $request->input('nickname', "");
    if ($nickname === "") return ['success' => false, 'msg' => 'Username empty!'];
    if (time() > (strtotime($date->date) - 20 * 60)) return ['success' => false, 'msg' => 'Registration too late (less than 20 minutes before the game)!'];
    $p = Player::where('nickname', $nickname)->first();
    if (!$p && $date->step === 1) {
      $p = new Player();
      $p->nickname = $nickname;
      $p->new = 1;
      $p->save();
    } else {
      if($date->step > 1){
        // check if player registered other $date->step games substracting tickets then
        $open = 0;
        $ndates = GameDate::getNextGamesByStep($date->step); 
        if(!is_null($ndates)){
          foreach($ndates as $ndate){
            if(!is_null($ndate->regs)){
              foreach($ndate->regs as $reg){
                if($reg->player_id == $p->id) $open++;
              }
            }
          }
        }
        if(($p["s" . $date->step . "_tickets"] - $open) < 1){
          return ['success' => false, 'msg' => 'Not enough tickets!'];
        }
      }
    }
    if (auth() && auth()->user() && auth()->user()->role === 's') {
      if (Registration::where([['game_date_id', $date->id], ['player_id', $p->id]])->first()) {
        return ['success' => false, 'msg' => 'Already registered!'];
      }
    } else {
      if (Registration::where('game_date_id', $date->id)
          ->where(function($query) use ($p, $request) {
              $query->where('player_id', $p->id)
                  ->orWhere('ip', $request->ip())
                  ->orWhere('fp', $request->input('fp'));
          })->first()) return ['success' => false, 'msg' => 'Already registered!'];
    }
    $reg = new Registration();
    $reg->game_date_id = $date->id;
    $reg->player_id = $p->id;
    $reg->fp = $request->input('fp', '');
    $reg->ip = $request->ip();
    $reg->save();
    return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
  }
}
