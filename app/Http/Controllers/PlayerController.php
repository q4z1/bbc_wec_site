<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerAward;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $player)
    {
        $player = Player::where('nickname', $player)->first();
        if(!$player) return abort(404);

        $awards = PlayerAward::where('player_id', $player->id)->with('award')->get()
        ->map(function ($a) {
            return $a->award;
        });

        return view('player', [
            "player" => $player,
            "stats" => $this->stats($player, date('Y'), date('m')),
            'awards' => $awards
        ]);
    }

    public function all(Request $request){
        $pagesize = $request->input('pageSize', 10);
        if($request->method() == 'GET'){
            return view('players', [
                'players' => Player::limit($pagesize)->orderBy('nickname')->get(),
                'total' => Player::count()
            ]);
        }else{
            $nickname = $request->input('nickname');
            $page = $request->input('page', 1);
            
            $sort = $request->input('sort', ['prop' => 'nickname', 'order' => 'ascending']);

            $total = Player::get()->count();

    
            $query = Player::orderBy($sort['prop'], (($sort['order'] == 'descending') ? 'DESC' : 'ASC'))
            ->offset(($page-1)*$pagesize)->limit($pagesize);
            if(!empty($nickname)){
                $query->where('nickname', 'LIKE', $nickname . '%');
            }
            $players = $query->get()->map(function($player){
                return $player;
            });

            return ['success' => true, 'total' => $total, 'players' => $players];
        }

    }

    public function stats(Player $player, $year=0, $month=0, $nocache=false)
    {
        if($nocache) Cache::forget('player.' . $player->id . "_" . $year . "_" . $month);
        return Cache::remember('player.' . $player->id . "_" . $year . "_" . $month, now()->addHours(24), function () use ($player, $year, $month, $nocache) {
            $games_alltime = Point::where('player_id', $player->id)->count();
            $stat_month = $stat_year = $stat_alltime = ['points' => 0, 'games' => 0];
            $avg_games_month = $avg_games_year = $avg_games_alltime = $sc_month = $sc_year = $sc_alltime = 0;
            $sum_month = $sum_year = $sum_alltime = ['players' => 0, 'games' => 0];
            $m = $y = 1;
            if ($year > 0) {
                if ($month > 0) {
                    $stats_month = Point::where('player_id', $player->id)
                        ->whereBetween('game_started', [
                            date($year . '-' . $month. '-01 00:00:00', time()),
                            date($year . '-' . $month. '-31 23:59:59', time())
                        ])
                        ->get();
                    // $games = [];
                    foreach ($stats_month as $stat) {
                            $stat_month['points'] += $stat->points;
                            $stat_month['games'] += 1;
                            // $games[] = $stat->game_id;
                    }
                    if($nocache) Cache::forget('sum_month' . "_" . $year . "_" . $month);
                    $sum_month = Cache::remember('sum_month' . "_" . $year . "_" . $month, now()->addHours(24), function () use ($year, $month) {
                        $mp = Point::whereBetween('game_started', [
                            date($year . '-' . $month. '-01 00:00:00', time()),
                            date($year . '-' . $month. '-31 23:59:59', time())
                        ])
                        ->get();
                        $pm = [];
                        foreach($mp as $pt){
                            if(!in_array($pt->player_id, $pm)) $pm[] = $pt->player_id;
                        }
                        return ['players' => count($pm), 'games' => count($mp)];
                    });
                    if ($stat_month['games'] > 0) {
                        $avg_games_month = round($sum_month['games'] / $sum_month['players']);
                        $sc_month = number_format($stat_month['points'] / (1 + $stat_month['games'] + max(($avg_games_month - $stat_month['games']), 0)), 2);
                    }
                }
                $stats_year = Point::where('player_id', $player->id)
                    ->whereBetween('game_started', [
                        date($year . '-01-01 00:00:00'),
                        date($year . '-12-31 23:59:59')
                    ])
                    ->get();
                // $games = [];
                foreach ($stats_year as $stat) {
                    $stat_year['points'] += $stat->points;
                    $stat_year['games'] += 1;
                    // $games[] = $stat->game_id;
                }
                if($nocache) Cache::forget('sum_year' . "_" . $year);
                $sum_year = Cache::remember('sum_year' . "_" . $year, now()->addHours(24), function () use ($year) {
                    $yp = Point::whereBetween('game_started', [
                        date($year . '-01-01 00:00:00'),
                        date($year . '-12-31 23:59:59')
                    ])
                    ->get();
                    $py = [];
                    foreach($yp as $pt){
                        if(!in_array($pt->player_id, $py)) $py[] = $pt->player_id;
                    }
                    return ['players' => count($py), 'games' => count($yp)];
                });
                $months = date('m');
                if($year === 2012) $months = 10; // WeC started 2012 March 9th
                elseif($year != date("Y")){
                    $months = 12;
                }

                if ($stat_year['games'] > 0) {
                    $avg_games_year = round($sum_year['games'] / $sum_year['players']);
                    $sc_year = number_format($stat_year['points'] / ($months + $stat_year['games'] + max(($avg_games_year - $stat_year['games']), 0)), 3);
                }
            } else {
                // $year = 0 => alltime
                $ts1 = strtotime('2012-01-01');
                $ts2 = strtotime(date('Y-m-d'));
                $y1 = date('Y', $ts1);
                $y2 = date('Y', $ts2);
                // $month1 = date('m', $ts1);
                $month2 = date('m', $ts2);
                $stats_alltime = Point::where('player_id', $player->id)
                    ->whereBetween('game_started', [
                        date($y1 . '-01-01 00:00:00'),
                        date($y2 . '-12-31 23:59:59')
                    ])
                    ->get();
                // $games = [];
                foreach ($stats_alltime as $stat) {
                    $stat_alltime['points'] += $stat->points;
                    $stat_alltime['games'] += 1;
                    // $games[] = $stat->game_id;
                }
                if($nocache) Cache::forget('sum_alltime');
                $sum_alltime = Cache::remember('sum_alltime', now()->addHours(24), function () use ($y1, $y2) {
                    $ap =  Point::whereBetween('game_started', [
                        date($y1 . '-01-01 00:00:00'),
                        date($y2 . '-12-31 23:59:59')
                    ])
                    ->get();
                    $pa = [];
                    foreach($ap as $pt){
                        if(!in_array($pt->player_id, $pa)) $pa[] = $pt->player_id;
                    }
                    return ['players' => count($pa), 'games' => count($ap)];
                });
                $y = (($y2 - $y1) * 12) + ($month2/* - $month1*/);
                $y -= 2; // @INFO: 2012 10 months only - substracting 2 (WeC started 2012 March 9th)
                if ($stat_alltime['games'] > 0) {
                    $avg_games_year = $avg_games_alltime = round($sum_alltime['games'] / $sum_alltime['players']);
                    $sc_year = $sc_alltime = number_format($stat_alltime['points'] / ($y + $stat_alltime['games'] + max(($avg_games_alltime - $stat_alltime['games']), 0)), 2);
                }
                $stat_year = $stat_alltime;
            }

            $stats =
                [
                    'score_month' => $sc_month,
                    'score_year' => $sc_year,
                    'score_alltime' => $sc_alltime,
                    'month' => $stat_month,
                    'year' => $stat_year,
                    'alltime' => $stat_alltime,
                    'avg_games_month' => $avg_games_month,
                    'avg_games_year' => $avg_games_year,
                    'avg_games_alltime' => $avg_games_alltime,
                    'games_alltime' => $games_alltime,
                    'player' => $player,
                    'sum_players_year' => $sum_year['players'],
                    'sum_players_month' => $sum_month['players'],
                    'sum_players_alltime' => $sum_alltime['players'],
                ];
            if($nocache){
                $stats = [
                    'year' => $year,
                    'score_month' => $sc_month,
                    'score_year' => $sc_year,
                    'score_alltime' => $sc_alltime,
                    // 'stat_month' => $stat_month,
                    // 'stat_year' => $stat_year,
                    // 'stat_alltime' => $stat_alltime,
                    'games_alltime' => $games_alltime,
                    'sum_games_month' => $sum_month['games'],
                    'avg_games_month' => $avg_games_month,
                    'sum_games_year' => $sum_year['games'],
                    'avg_games_year' => $avg_games_year,
                    'sum_games_alltime' => $sum_alltime['games'],
                    'avg_games_alltime' => $avg_games_alltime,
                    'sum_players_year' => $sum_year['players'],
                    'sum_players_month' => $sum_month['players'],
                    'sum_players_alltime' => $sum_alltime['players'],
                ];
                
            }
            return $stats;
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function show(Player $player)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function edit(Player $player)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Player $player)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Player  $player
     * @return \Illuminate\Http\Response
     */
    public function destroy(Player $player)
    {
        //
    }
}