<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\User;
use App\Models\Registration;
use App\Models\ShoutBoxMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FingerprintNickname extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return view('fpnicksearch');
  }

  public function search(Request $request){

    $fp = $request->input('fp', null);
    $nickname = $request->input('nickname', null);
    $success = true;
    $result = null;
    if($fp !== null){
      $sb = ShoutBoxMessage::where("fp", "=", $fp)->get()->map(function ($a) {
        return $a->nickname;
      });
      $reg = Registration::where("fp", "=", $fp)->get()->map(function ($a) {
        $p = Player::where("id", "=", $a->player_id)->get()->first();
        if(is_null($p)) return "n/a";
        return $p->nickname;
      });
      $sb = array_unique($sb->toArray());
      $reg = array_unique($reg->toArray());
      $result = ["sb" => $sb, "reg" => $reg];
    }else if($nickname !== null){
      $pl = Player::where("nickname", "=", $nickname)->get()->first();
      $u = User::where("name", "=", $nickname)->get()->first();
      $sb = ShoutBoxMessage::where("user_id", "=", $u->id)->get()->map(function ($a) {
        return $a->fp;
      });
      $reg = Registration::where("player_id", "=", $pl->id)->get()->map(function ($a) {
        return $a->fp;
      });
      $sb = array_unique($sb->toArray());
      $reg = array_unique($reg->toArray());
      $result = ["sb" => $sb, "reg" => $reg];
    }else{
      $success = false;
      $result = "Missing parameter!";
    }
    return ['success' => true, 'result' => $result];
  }
}
