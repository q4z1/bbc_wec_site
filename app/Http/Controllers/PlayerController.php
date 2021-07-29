<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerAward;
use App\Models\Point;
use App\Models\Season;
use App\Http\Controllers\SeasonController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
class PlayerController extends Controller
{
    private $season = null;

    public function __construct()
    {
      $this->middleware('auth', ['only' => ['delete', 'tickets']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $player)
    {
        $player = Player::where('nickname', $player)->first();

        $awards = PlayerAward::where('player_id', $player->id)->with('award')->get()
        ->map(function ($a) {
            return $a->award;
        });

        $this->season = Season::orderBy('start', 'DESC')->first();
        $season = $this->season->id;
        $stats = $this->stats($player, $season);
        return view('player', [
            "player" => $player,
            "season" => $season,
            'stats' => $stats,
            'awards' => $awards
        ]);
    }

    public function all(Request $request){
        if($request->method() == 'GET'){
            return view('players');
        }else{
            $filters = $request->input('filters');
            $page = $request->input('page', 1);
            $pagesize = $request->input('pageSize', 50);
            $sort = $request->input('sort');

            $total = Player::where('new', 0)->get()->count();

    
            $query = Player::where('new', 0)->orderBy($sort['prop'], (($sort['order'] == 'descending') ? 'DESC' : 'ASC'))
            ->offset(($page-1)*$pagesize)->limit($pagesize);
            if(!empty($filters)){
                $query->where('nickname', 'LIKE', $filters['value'] . '%');
            }
            $players = $query->get()->map(function($player){
                return $player;
            });

            return ['total' => $total, 'data' => $players];
        }

    }

    public function playerlist(Request $request){
        $res = DB::table('players')
        ->select(
            'players.id', 'players.nickname'
        )->orderBy('nickname', 'ASC')
        ->get();
        return $res;
    }

    public function tickets(Request $request, Player $player){
        $success = false;
        if($request->exists('s2') && $request->exists('s3') && $request->exists('s4')){
            $player->s2_tickets = $request->s2;
            $player->s3_tickets = $request->s3;
            $player->s4_tickets = $request->s4;
            $player->save();
            $success = true;
        }
        return ['success' => $success];
    }

    public function stats(Player $player, $season){
        return Cache::remember('player.' . $player->id . "_" . $season, now()->addHours(24), function() use($player, $season){
            $date_range = SeasonController::dateRange($season);

            $stats_season = Point::where('player_id', $player->id)
                ->whereBetween('game_started', [
                    $date_range['start'],
                    $date_range['end']
                ])
                ->get();
            $stats_alltime = Point::where('player_id', $player->id)
                ->get();
            $stat_season = $stat_alltime = ['points' => 0, 'games' => 0];
            foreach($stats_season as $stat){
                $stat_season['points'] += $stat->points;
                $stat_season['games'] += 1;
            }
            foreach($stats_alltime as $stat){
                $stat_alltime['points'] += $stat->points;
                $stat_alltime['games'] += 1;
            }

            $seasons = Season::orderBy('id', 'ASC')->get(); 
            
            $stats =
            [
                'score_season' => number_format($this->calc_score($stat_season['points'], $stat_season['games'])/1000, 2),
                'score_alltime' => number_format($this->calc_score($stat_alltime['points'], $stat_alltime['games'])/1000, 2),
                'points_season' => $stat_season['points'],
                'points_alltime' => $stat_alltime['points'],
                'games_season' => $stat_season['games'],
                'games_alltime' => $stat_alltime['games'],
                'player' => $player,
                'season' => $season,
                'seasons' => $seasons,
            ];
            return $stats;
        });
    }

    public function calc_score($points, $games){    
        if($games<=0 or $points<=0) return 0;
        $coefficient = 1 + log((float)$games, 2);//logarithm with base 2
        $score =(float)$points* $coefficient /(float)$games;
        return (int)($score*1000);
    }

    public function delete(Request $request, Player $player){
        $player->delete();
        return ['success' => true];
    }

}
