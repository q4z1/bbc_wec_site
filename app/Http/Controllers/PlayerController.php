<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResultController;
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

        $awards = $awards->sortBy('title', SORT_NATURAL)->values();

        $res = new ResultController();
        $all_month = $res->all_player_stats(date('Y'), date('m'));
        $all_year = $res->all_player_stats(date('Y'));

        $stats_month = $this->stats($player, date('Y'), date('m'));
        $stats_year = $this->stats($player, date('Y'));

        $stats_extra = Cache::remember('player.' . $player->id, now()->addHours(24), function () use ($player, $all_month, $all_year) {
            $pos_month = $pos_year = 1;

            foreach($all_month as $one){
                if($one['player']->id == $player->id) break;
                $pos_month++;
            }
            foreach($all_year as $one){
                if($one['player']->id == $player->id) break;
                $pos_year++;
            }

            $games_alltime = Point::where('player_id', $player->id)->count();

            return ['pos_month' => $pos_month, 'pos_year' => $pos_year, 'games_alltime' => $games_alltime];
        });

        $stats_month['pos'] = $stats_extra['pos_month'];
        $stats_year['pos'] = $stats_extra['pos_year'];

        return view('player', [
            "player" => $player,
            "stats" => [
                'month' => $stats_month,
                'year' => $stats_year,
                'games_alltime' => $stats_extra['games_alltime'],
            ],
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
            $total = ['points' => 0, 'games' => 0];
            $avg_games = $score = 0;
            $sum = ['players' => 0, 'games' => 0];
            $m = $y = 1;
            if ($year > 0 && $month > 0) {
                $stats_month = Point::where('player_id', $player->id)
                    ->whereBetween('game_started', [
                        date($year . '-' . $month. '-01 00:00:00', time()),
                        date($year . '-' . $month. '-31 23:59:59', time())
                    ])
                    ->get();
                // $games = [];
                foreach ($stats_month as $stat) {
                    $total['points'] += $stat->points;
                    $total['games'] += 1;
                    // $games[] = $stat->game_id;
                }
                if($nocache) Cache::forget('sum_month' . "_" . $year . "_" . $month);
                $sum = Cache::remember('sum_month' . "_" . $year . "_" . $month, now()->addHours(24), function () use ($year, $month) {
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
                if ($total['games'] > 0) {
                    $avg_games = round($sum['games'] / $sum['players']);
                    $score = number_format($total['points'] / (1 + $total['games'] + max(($avg_games - $total['games']), 0)), 2);
                }
            }
            else if ($year > 0) {
                // $month = 0 => allyear
                $stats_year = Point::where('player_id', $player->id)
                    ->whereBetween('game_started', [
                        date($year . '-01-01 00:00:00'),
                        date($year . '-12-31 23:59:59')
                    ])
                    ->get();
                // $games = [];
                foreach ($stats_year as $stat) {
                    $total['points'] += $stat->points;
                    $total['games'] += 1;
                    // $games[] = $stat->game_id;
                }
                if($nocache) Cache::forget('sum_year' . "_" . $year);
                $sum = Cache::remember('sum_year' . "_" . $year, now()->addHours(24), function () use ($year) {
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

                if ($total['games'] > 0) {
                    $avg_games = round($sum['games'] / $sum['players']);
                    $score = number_format($total['points'] / ($months + $total['games'] + max(($avg_games - $total['games']), 0)), 3);
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
                    $total['points'] += $stat->points;
                    $total['games'] += 1;
                    // $games[] = $stat->game_id;
                }
                if($nocache) Cache::forget('sum_alltime');
                $sum = Cache::remember('sum_alltime', now()->addHours(24), function () use ($y1, $y2) {
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
                if ($total['games'] > 0) {
                    $avg_games = round($sum['games'] / $sum['players']);
                    $score = number_format($total['points'] / ($y + $total['games'] + max(($avg_games - $total['games']), 0)), 2);
                }
            }

            $stats =
                [
                    'score' => $score,
                    'games' => $total['games'],
                    'avg_games' => $avg_games,
                    'player' => $player,
                ];
            if($nocache){
                $stats = [
                    'year' => $year,
                    'month' => $month,
                    'score' => $score,
                    'games' => $total['games'],
                    'sum_games' => $sum['games'],
                    'avg_games' => $avg_games,
                    'sum_players' => $sum['players'],
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