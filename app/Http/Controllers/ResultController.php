<?php

namespace App\Http\Controllers;

use App\Http\Controllers\LogFileController;
use App\Http\Controllers\SeasonController;
use App\Models\Game;
use App\Models\Player;
use App\Models\Point;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth', ['only' => ['game_edit']]);
  }

    public function index(Request $request){
        $season = Season::orderBy('start', 'DESC')->first()->id;
        $sr = SeasonController::dateRange($season);
        $query = DB::table('games')
        ->leftJoin('players as p1', 'games.pos1', '=', 'p1.id')
        ->leftJoin('players as p2', 'games.pos2', '=', 'p2.id')
        ->leftJoin('players as p3', 'games.pos3', '=', 'p3.id')
        ->leftJoin('players as p4', 'games.pos4', '=', 'p4.id')
        ->leftJoin('players as p5', 'games.pos5', '=', 'p5.id')
        ->leftJoin('players as p6', 'games.pos6', '=', 'p6.id')
        ->leftJoin('players as p7', 'games.pos7', '=', 'p7.id')
        ->leftJoin('players as p8', 'games.pos8', '=', 'p8.id')
        ->leftJoin('players as p9', 'games.pos9', '=', 'p9.id')
        ->leftJoin('players as p10', 'games.pos10', '=', 'p10.id')
        ->select(
            'games.type', 'games.number', 'games.started',
            'p1.nickname as p1',
            'p2.nickname as p2',
            'p3.nickname as p3',
            'p4.nickname as p4',
            'p5.nickname as p5',
            'p6.nickname as p6',
            'p7.nickname as p7',
            'p8.nickname as p8',
            'p9.nickname as p9',
            'p10.nickname as p10'
        )->orderBy('number', 'DESC')
        ->whereBetween('started', [
            $sr['start'],
            $sr['end']
        ]);
        $totals = $query->count();
        $results = $query->limit(10)
            ->get();
        $params = [
          "results" => $results,
          "totals" => $totals,
          "seasons" => Season::orderBy('id', 'ASC')->pluck('id'),
          "season" => $season
        ];
        return view('results', $params);
    }

    public function halloffame(Request $request){
        $totals = DB::table('players')
        ->select(
            'players.id', 'players.nickname', 'players.avatar'
        )
        ->count();
        $results = DB::table('players')
        ->select(
            'players.id', 'players.nickname', 'players.avatar'
        )->orderBy('id', 'ASC')
        ->limit(100)
        ->get();
        return view('halloffame', [
            "results" => $results,
            "totals" => $totals
        ]);
    }

    public function ranking(Request $request){
        $season = $request->input('season', Season::orderBy('start', 'DESC')->first()->id);

        $stats = $this->all_player_stats($season);

        if($request->isMethod('post')) return ['success' => true, 'stats' => $stats];

        return view('ranking', [
            "stats" => $stats,
            "season" => $season,
            "seasons" => Season::orderBy('id', 'ASC')->pluck('id')
        ]);
    }

    public function all_player_stats($season=0, $sort=true){
        $sort = ($sort) ? 1 : 0;
        return Cache::remember('all_player_stats_'.$season.'_'.$sort, now()->addHours(24), function() use($season, $sort){
            $all_stats = [];
            $pc = new PlayerController();
            $players = Player::get();
            foreach($players as $player){
                $stat = $pc->stats($player, $season);
                if($stat['games'] > 0){
                    $all_stats[$player->id] = $stat;
                }
            }
            if($sort){
                usort($all_stats, function($a, $b) use ($season) {
                    if($season >= 9){
                        // sort 1st by min games check (S1 >= 20 & games >= 40)
                        $acheck = ($a['step1'] >= 20 && $a['games'] >= 40);
                        $bcheck = ($b['step1'] >= 20 && $b['games'] >= 40);
                        if($acheck != $bcheck) return ($acheck) ? -1 : 1;
                    }
                    return  [ $b['score'], strtolower($a['nickname']) ] <=>
                            [ $a['score'], strtolower($b['nickname']) ];
                });
            }
            return $all_stats;
        });
    }

    public function game(Request $request, $game){
        $game = Game::where('number', $game)->first();
        if(!$game) return abort(404);
        for($i=1;$i<=10;$i++){
            $p = "pos$i";
            $pl = Player::find($game->{$p});
            if($pl) $pl = $pl->nickname;
            $game->{$p} = $pl;
        }
        $log = new LogFileController();
        $game->stats = $log->process_log_file($game->pdb, $game->unique_game_id);
        return view('game', [
            "game" => $game
        ]);
    }

    public function game_edit(Request $request, $game){
        $game = Game::where('number', $game)->first();
        if(!$game) return abort(404);
        for($i=1;$i<=10;$i++){
            $p = "pos$i";
            $pl = Player::find($game->{$p});
            if($pl) $pl = $pl->nickname;
            $game->{$p} = $pl;
        }
        $log = new LogFileController();
        $game->stats = $log->process_log_file($game->pdb, null);
        return view('game_edit', [
            "game" => $game
        ]);
    }

    public function filter(Request $request, Player $player=null){
        $alltime = $request->input('alltime', false);
        $season = $request->input('season', Season::orderBy('start', 'DESC')->first()->id);
        $sr = SeasonController::dateRange($season);
        $page = $request->input('page', 1);
        $type = $request->input('type', 1);
        $query = DB::table('games')
        ->leftJoin('players as p1', 'games.pos1', '=', 'p1.id')
        ->leftJoin('players as p2', 'games.pos2', '=', 'p2.id')
        ->leftJoin('players as p3', 'games.pos3', '=', 'p3.id')
        ->leftJoin('players as p4', 'games.pos4', '=', 'p4.id')
        ->leftJoin('players as p5', 'games.pos5', '=', 'p5.id')
        ->leftJoin('players as p6', 'games.pos6', '=', 'p6.id')
        ->leftJoin('players as p7', 'games.pos7', '=', 'p7.id')
        ->leftJoin('players as p8', 'games.pos8', '=', 'p8.id')
        ->leftJoin('players as p9', 'games.pos9', '=', 'p9.id')
        ->leftJoin('players as p10', 'games.pos10', '=', 'p10.id')
        ->select(
            'games.type', 'games.number', 'games.started',
            'p1.nickname as p1',
            'p2.nickname as p2',
            'p3.nickname as p3',
            'p4.nickname as p4',
            'p5.nickname as p5',
            'p6.nickname as p6',
            'p7.nickname as p7',
            'p8.nickname as p8',
            'p9.nickname as p9',
            'p10.nickname as p10'
        )->orderBy('number', 'DESC');
        if(!$alltime){
            $query = $query->whereBetween('started', [
                $sr['start'],
                $sr['end']
            ]);
        }
        if($type){
            $query = $query->where('type', $type);
        }
        if($player){
            $query = $query->where(function($q) use ($player) {
                $q->where('pos1', '=', $player->id)
                ->orWhere('pos2', '=', $player->id)
                ->orWhere('pos3', '=', $player->id)
                ->orWhere('pos4', '=', $player->id)
                ->orWhere('pos5', '=', $player->id)
                ->orWhere('pos6', '=', $player->id)
                ->orWhere('pos7', '=', $player->id)
                ->orWhere('pos8', '=', $player->id)
                ->orWhere('pos9', '=', $player->id)
                ->orWhere('pos10', '=', $player->id);
            });
        }
        $total = $query->count();
        $results = $query->offset(($page-1)*10)
        ->take(10)
        ->get();
        return ['success' => true, 'result' => $results, 'total' => $total];
    }

    public function halloffame_filter(Request $request){
        $year = $request->input('year', 0);
        if($year == 0){
            $totals = DB::table('players')
            ->select(
                'players.id', 'players.nickname', 'players.avatar'
            )
            ->count();
            $results = DB::table('players')
            ->select(
                'players.id', 'players.nickname', 'players.avatar'
            )->orderBy('id', 'ASC')
            ->take(100)
            ->get();
        }else{
            // @TODO: where year ...
            $totals = DB::table('players')
            ->select(
                'players.id', 'players.nickname', 'players.avatar'
            )
            ->count();
            $results = DB::table('players')
            ->select(
                'players.id', 'players.nickname', 'players.avatar'
            )->orderBy('id', 'ASC')
            ->take(100)
            ->get();
        }

        return ['success' => true, 'result' => $results];
    }
}
