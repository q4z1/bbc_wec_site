<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Point;
use App\Models\Season;
use App\Models\Action;
use App\Http\Controllers\LogFileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
                // ticket handling
                switch($g->type){
                    case(1):
                        if($i < 3){
                            $player->s2_tickets += 1;
                        }
                        if($player->s2_tickets > 10) $player->s2_tickets = 10;
                        break;
                    case(2):
                        if($i != 3){
                            $player->s2_tickets -= 1;
                        }
                        if($i < 3){
                            $player->s3_tickets += 1;
                        }
                        if($player->s3_tickets > 10) $player->s3_tickets = 10;
                        break;
                    case(3):
                        // if($i != 3){
                            $player->s3_tickets -= 1;
                        // }
                        if($i < 3){
                            $player->s4_tickets += 1;
                        }
                        if($player->s4_tickets > 10) $player->s4_tickets = 10;
                        break;
                    case(4):
                        $player->s4_tickets -= 1;
                        break;
                }
                $player->new = 0;
                $player->save();

                $pt = new Point();
                $pt->points = 0;
                $points = [10,9,8,7,6,5,4,3,2,1];
                $pt->points = $points[$i-1] * $g->type;
                $pt->game_started = $payload["date"] . " " . $payload["time"];
                $pt->game_id = $g->id;
                $pt->pos = $i;
                $pt->type = $g->type;
                $pt->player_id = $player->id;
                $pt->save();


            }
        }
        if($g->type == 4){
            $season = new Season();
            $season->start = date("Y-m-d H:i:s");
            $season->save();

            // @INFO: 2023-08-02 - delete all tickets
            $pls = Player::get();
            foreach($pls as $p){
                if($p->s2_tickets || $p->s3_tickets || $p->s4_tickets){
                    $p->s2_tickets = 0;
                    $p->s3_tickets = 0;
                    $p->s4_tickets = 0;
                    $p->updated_at = date("Y-m-d H:i:s");
                    $p->save();
                }
            }
        }
        $action = new Action();
        $action->action = "Step" . $g->type . " game #" . $g->number . " uploaded.";
        $action->reason = "n/a"; // @TODO: reason handling
        $action->user = Auth::id();
        $action->save();
        $this->syncTickets();
        Cache::flush();
        return ["status" => true, 'msg' => $g];
    }

    private function preview($url){
        $log = new LogFileController();
        $game = $log->process_log($url);
        return ["status" => true, "msg" => $game['player_list']];
    }

    private function syncTickets(): void
    {
        $rows = Player::select(
                'players.nickname',
                'players.s2_tickets',
                'players.s3_tickets',
                'players.s4_tickets',
                'players.id'
            )
            ->join('points', 'players.id', '=', 'points.player_id')
            ->distinct()
            ->orderBy('players.nickname')
            ->get();

        $lines = $rows
            ->map(fn($p) => "{$p->nickname}\t{$p->s2_tickets}\t{$p->s3_tickets}\t{$p->s4_tickets}\t{$p->id}\t{$p->id}")
            ->unique()
            ->values();

        file_put_contents(
            public_path('/exp3/bbcbot/minidb.txt'),
            $lines->implode("\n")
        );
    }

    /**
     * game upload view
     */
    public function upload_view()
    {
        $last = Game::orderBy('number', 'DESC')->first();
        $gameno = 1234567890;
        if($last) $gameno = $last->number + 1;
        return view('game_upload', ['last' => $gameno]);
    }

    public function delete_game(Request $request, $game)
    {
        $game = Game::where('number', $game)->first();
        if(!$game) return ["status" => false, 'msg' => "Game not found!"];
        $out = [];
        for($i=1;$i<11;$i++){
          $player = Player::where("id", $game->{"pos$i"})->first();
          $out[$i] = "";
          switch($game->type){
            case(1):
              if($i < 3){
                $player->s2_tickets = max($player->s2_tickets-1, 0);
              }
              break;
            case(2):
              if($i === 3) break;
              if($i < 3){
                $player->s3_tickets = max($player->s3_tickets-1, 0);
              }
              ++$player->s2_tickets;
              break;
            case(3):
              // if($i === 3) break;
              if($i < 3){
                $player->s4_tickets = max($player->s4_tickets-1, 0);
              }
              ++$player->s3_tickets;
              break;
            case(4):
              break;
          }
          $player->save();
        }
        $action = new Action();
        $action->action = "Step" . $game->type . " game #" . $game->number . " deleted.";
        $action->reason = $request->input('reason', "n/a");
        $action->user = Auth::id();
        $action->save();
        Point::where('game_id', $game->id)->delete();
        $game->delete();
        $this->syncTickets();
        Cache::flush();
        return ["status" => true, 'msg' => "Game deleted!"];
    }

    public function update_game(Request $request, $game)
    {
        $game = Game::where('number', $game)->first();
        if(!$game) return ["status" => false, 'msg' => "Game not found!"];

        // json gedöns
        $payload = json_decode($request->getContent(), true);

        if(is_null($payload)) return ["status" => false, 'msg' => "Parameter missing!"];

        $game->started = $payload["date"] . " " . $payload["time"];
        $game->type = $payload['gametype'];
        $game->number = $payload['gameno'];
        $game->save();
        $action = new Action();
        $action->action = "Step" . $game->type . " game #" . $game->number . " updated.";
        $action->reason = $request->input('reason', "n/a");
        $action->user = Auth::id();
        $action->save();    
        $this->syncTickets();
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
