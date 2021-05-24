<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Point;
use App\Http\Controllers\LogFileController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * game upload ajax
     */
    public function upload(Request $request)
    {
        // json gedöns
        $payload = json_decode($request->getContent(), true);

        if(is_null($payload)){
            return ["status" => false, 'msg' => "Parameter!"];
        }

        if($payload['preview']){
            return $this->preview($payload["loglink"]);
        }
        $log = new LogFileController();
        $game = $log->process_log($payload["loglink"]);
        $g = new Game();
        $g->pdb = $game['pdb'];
        for($i=1;$i<=10;$i++){
            $p = "pos" . $i;
            if(array_key_exists($i-1, $game['player_list'][1])){
                $player = Player::where('nickname', $game['player_list'][1][$i-1])->first();
                if(!$player){
                    $player = new Player();
                    $player->nickname = $game['player_list'][1][$i-1];
                    $player->save();
                }
                $g->{$p} = $player->id;
            } else{
                $g->{$p} = 0;
            }
        }
        $g->started = $payload["date"] . " " . $payload["time"];
        $g->type = $payload['gametype'];
        $g->number = $payload['gameno'];
        $g->unique_game_id = $game['game_id'];
        $g->save();
        // score
        for($i=1;$i<=10;$i++){
            if(array_key_exists($i-1, $game['player_list'][1])){
                $player = Player::where('nickname', $game['player_list'][1][$i-1])->first();
                $pt = new Point();
                $pt->points = 0;
                if($i<8){
                    $points = [75,45,30,20,15,10,5];
                    $pt->points = $points[$i-1];
                }
                $pt->game_started = $payload["date"] . " " . $payload["time"];
                $pt->game_id = $g->id;
                $pt->pos = $i;
                $pt->type = $g->type;
                $pt->player_id = $player->id;
                $pt->save();
            }
        }
        Cache::flush();
        return ["status" => true, 'msg' => $g];
    }

    private function preview($url){
        $log = new LogFileController();
        $game = $log->process_log($url);
        return ["status" => true, "msg" => $game['player_list']];
    }

    /**
     * game upload view
     */
    public function upload_view()
    {
        $last = Game::orderBy('number', 'DESC')->first();
        $gameno = 123456;
        if($last) $gameno = $last->number + 1;
        return view('upload.game', ['last' => $gameno]);
    }

    public function delete_game(Request $request, $game)
    {
        $game = Game::where('number', $game)->first();
        if(!$game) return ["status" => false, 'msg' => "Game not found!"];
        Point::where('game_id', $game->id)->delete();
        $game->delete();
        return ["status" => true, 'msg' => "Game deleted!"];
    }

    public function update_game(Request $request, $game)
    {
        $game = Game::where('number', $game)->first();
        if(!$game) return ["status" => false, 'msg' => "Game not found!"];

        // json gedöns
        $payload = json_decode($request->getContent(), true);

        if(is_null($payload)) return ["status" => false, 'msg' => "Parameter missing!"];

        for($i=1;$i<=10;$i++){
            $p = "pos" . $i;
            if(array_key_exists($i-1, $payload['player'])){
                $player = Player::where('nickname', $payload['player'][$i-1])->first();
                if($player) $game->{$p} = $player->id;
            }
        }
        $game->started = $payload["date"] . " " . $payload["time"];
        $game->type = $payload['gametype'];
        $game->number = $payload['gameno'];
        $game->save();
        // score
        Point::where('game_id', $game->id)->delete();
        for($i=1;$i<=10;$i++){
            if(array_key_exists($i-1, $payload['player'])){
                $player = Player::where('nickname', $payload['player'][$i-1])->first();
                if($player){
                    $pt = new Point();
                    $pt->points = 0;
                    if($i<8){
                        $points = [75,45,30,20,15,10,5];
                        $pt->points = $points[$i-1];
                    }
                    $pt->game_started = $payload["date"] . " " . $payload["time"];
                    $pt->game_id = $game->id;
                    $pt->pos = $i;
                    $pt->type = $game->type;
                    $pt->player_id = $player->id;
                    $pt->save();
                }
            }
        }
        Cache::flush();
        return ["status" => true, 'msg' => "Game succesfully saved!"];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function destroy(Game $game)
    {
        //
    }
}
