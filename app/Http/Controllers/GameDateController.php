<?php

namespace App\Http\Controllers;

use App\Models\GameDate;
use App\Models\Player;
use App\Models\User;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameDateController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth', ['except' => ['get']]);
  }

  public function get(GameDate $date)
  {
    $gd = GameDate::where('id', $date->id)->with('regs.player')->first();
    $admin = false;
    $j = 0;
    for ($i=0; $i < count($gd->regs); $i++) {
      if ($j % 10 === 0) $admin = false;
      $j++;
      if(is_null($gd->regs[$i]->player)){
        $gd->regs[$i]->player = new Player();
        $gd->regs[$i]->player->nickname = "deleted";
      }
      $u = User::where('name', $gd->regs[$i]->player->nickname)->first();
      if (!$admin && $u && in_array($u->role, ['a', 's'])) {
        $gd->regs[$i]->player->admin = true;
        $admin = true;
      } else {
        $gd->regs[$i]->player->admin = false;
      }
      if ((auth() && auth()->user() && in_array(auth()->user()->role, ['a','s'])) || ($u && auth() && auth()->id() == $u->id && (time() < (strtotime($gd->date) - 60 * 60)))) $gd->regs[$i]->player->owner = true;
      $j++;
    }
    return  ['success' => true, 'date' => $gd, 'utcDate' => gmdate('Y-m-d H:i:s', strtotime($gd->date))];
  }

  public function add(Request $request)
  {
    if (!in_array(auth()->user()->role, ['a', 's'])) return ['success' => false, 'msg' => 'Not valid.'];
    $date = $request->input('date', null);
    $step = $request->input('step', null);
    if (!$date || !is_numeric($step)) return ['success' => false, 'msg' => 'Missing paramter(s).'];
    $d = GameDate::where([['date', $date], ['step', $step]])->first();
    if ($d) return ['success' => false, 'msg' => 'Game already exists.'];
    elseif (strtotime($date) < strtotime("now")) return ['success' => false, 'msg' => 'Date/Time is in the past.'];
    $d = new GameDate();
    $d->date = $date;
    $d->step = $step;
    $d->title =  $request->input('title', null);
    $d->save();
    $action = new Action();
    $action->action = "GameDate " . $date . "/Step" . $step . " created.";
    $action->reason = "n/a"; // @TODO: reason handling
    $action->user = Auth::id();
    $action->save();
    return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
  }

  public function update(Request $request, $gameDate)
  {
    if (!in_array(auth()->user()->role, ['a','s'])) return ['success' => false, 'msg' => 'Not valid.'];
    $date = $request->input('date', null);
    $step = $request->input('step', null);
    if (!$date || !$step) return ['success' => false, 'msg' => 'Missing paramter(s).'];
    $d = GameDate::where([['date', $date], ['step', $step]])->first();
    if ($d) return ['success' => false, 'msg' => 'Game already exists.'];
    elseif (strtotime($date) < strtotime("now")) return ['success' => false, 'msg' => 'Date/Time is in the past.'];
    $gameDate->step = $step;
    $gameDate->date = $date;
    $gameDate->save();
    $action = new Action();
    $action->action = "GameDate " . $date . "/Step" . $step . " updated.";
    $action->reason = "n/a"; // @TODO: reason handling
    $action->user = Auth::id();
    $action->save();
    return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
  }

  public function delete(GameDate $date)
  {
    if (!in_array(auth()->user()->role, ['a','s'])) return ['success' => false, 'msg' => 'Not valid.'];
    $action = new Action();
    $action->action = "GameDate " . $date->date . "/Step" . $date->step . " deleted.";
    $action->reason = "n/a"; // @TODO: reason handling
    $action->user = Auth::id();
    $action->save();
    GameDate::where('id', $date->id)->first()->delete();
    return ['success' => true, 'dates' => GameDate::getUpcomingGames()];
  }
}
