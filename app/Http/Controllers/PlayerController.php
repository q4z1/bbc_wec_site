<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResultController;
use App\Models\Game;
use App\Models\Player;
use App\Models\PlayerAward;
use App\Models\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $stats_extra = Cache::remember('player.' . $player->id, now()->addHours(24), function () use ($player) {
            $pos_month = $pos_year = $pos_alltime = 1;
            $res = new ResultController();

            $all = $res->all_player_stats(date('Y'), date('m'));
            foreach($all as $one){
                if($one['player_id'] == $player->id) break;
                $pos_month++;
            }
            if($pos_month > count($all)) $pos_month = '';

            $all = $res->all_player_stats(date('Y'));
            foreach($all as $one){
                if($one['player_id'] == $player->id) break;
                $pos_year++;
            }
            if($pos_year > count($all)) $pos_year = '';

            $all = $res->all_player_stats(); // default => alltime
            foreach($all as $one){
                if($one['player_id'] == $player->id) break;
                $pos_alltime++;
            }
            if($pos_alltime > count($all)) $pos_alltime = '';

            return ['pos_month' => $pos_month, 'pos_year' => $pos_year, 'pos_alltime' => $pos_alltime];
        });

        $stats_month = $this->stats($player, date('Y'), date('m'), true);
        $stats_month['pos'] = $stats_extra['pos_month'];
        $stats_year = $this->stats($player, date('Y'), 0, true);
        $stats_year['pos'] = $stats_extra['pos_year'];
        $stats_alltime = $this->stats($player, 0, 0, true);
        $stats_alltime['pos'] = $stats_extra['pos_alltime'];

        return view('player', [
            "player" => $player,
            "stats" => [
                'month' => $stats_month,
                'year' => $stats_year,
                'alltime' => $stats_alltime,
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

            $total = Player::count();

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

    private function calculateTotals(Collection $collection)
    {
        $plucked = $collection->pluck('points');
        $points = $plucked->sum();
        $games = $plucked->count();
        $places = [];
        if($games){
            $plucked = $collection->pluck('pos')->countBy()->all();
            for($i=1;$i<=10;$i++){
                $places[] = array_key_exists($i, $plucked) ? $plucked[$i] : 0;
            }
        }
        return ['points' => $points, 'games' => $games, 'places' => $places ];
    }

    public function stats(Player $player, $year=0, $month=0, $places=false, $nocache=false)
    {
        $places = ($places) ? 1 : 0;
        if($nocache) Cache::forget('player.' . $player->id . '_' . $year . '_' . $month . '_' . $places);
        return Cache::remember('player.' . $player->id . '_' . $year . '_' . $month . '_' . $places, now()->addHours(24), function () use ($player, $year, $month, $places, $nocache) {
            $total = ['points' => 0, 'games' => 0, 'places' => [] ];
            $avg_games = $score = 0;
            $m = $y = 1;
            if ($year > 0 && $month > 0) {
                $points = Point::where('player_id', $player->id)
                    ->whereBetween('game_started', [
                        date($year . '-' . $month. '-01 00:00:00', time()),
                        date($year . '-' . $month. '-31 23:59:59', time())
                    ]);
                if($places){
                    $points = $points->select('points', 'pos')->get();
                    $total = $this->calculateTotals($points);
                }else{
                    $points = $points->pluck('points');
                    $total['points'] = $points->sum();
                    $total['games'] = $points->count();
                }
                if($nocache) Cache::forget('avg_games' . '_' . $year . '_' . $month);
                $avg_games = Cache::remember('avg_games' . '_' . $year . '_' . $month, now()->addHours(24), function () use ($year, $month) {
                    $ids = Point::whereBetween('game_started', [
                        date($year . '-' . $month. '-01 00:00:00', time()),
                        date($year . '-' . $month. '-31 23:59:59', time())
                    ])
                    ->pluck('player_id')->all();
                    $games = count($ids); // count all ids => games
                    $players = count(array_flip($ids)); // count unique ids => players
                    return ($games) ? round($games / $players) : 0;
                });
                if ($total['games'] > 0) {
                    $score = number_format($total['points'] / (1 + $total['games'] + max(($avg_games - $total['games']), 0)), 2);
                }
            }
            else if ($year > 0) {
                // $month = 0 => allyear
                $points = Point::where('player_id', $player->id)
                    ->whereBetween('game_started', [
                        date($year . '-01-01 00:00:00'),
                        date($year . '-12-31 23:59:59')
                    ]);
                if($places){
                    $points = $points->select('points', 'pos')->get();
                    $total = $this->calculateTotals($points);
                }else{
                    $points = $points->pluck('points');
                    $total['points'] = $points->sum();
                    $total['games'] = $points->count();
                }
                if($nocache) Cache::forget('avg_games' . '_' . $year);
                $avg_games = Cache::remember('avg_games' . '_' . $year, now()->addHours(24), function () use ($year) {
                    $ids = Point::whereBetween('game_started', [
                        date($year . '-01-01 00:00:00'),
                        date($year . '-12-31 23:59:59')
                    ])
                    ->pluck('player_id')->all();
                    $games = count($ids); // count all ids => games
                    $players = count(array_flip($ids)); // count unique ids => players
                    return ($games) ? round($games / $players) : 0;
                });
                $months = date('m');
                if($year === 2012) $months = 10; // WeC started 2012 March 9th
                elseif($year != date("Y")){
                    $months = 12;
                }

                if ($total['games'] > 0) {
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
                $points = Point::where('player_id', $player->id)
                    ->whereBetween('game_started', [
                        date($y1 . '-01-01 00:00:00'),
                        date($y2 . '-12-31 23:59:59')
                    ]);
                if($places){
                    $points = $points->select('points', 'pos')->get();
                    $total = $this->calculateTotals($points);
                }else{
                    $points = $points->pluck('points');
                    $total['points'] = $points->sum();
                    $total['games'] = $points->count();
                }
                if($nocache) Cache::forget('avg_games_alltime');
                $avg_games = Cache::remember('avg_games_alltime', now()->addHours(24), function () use ($y1, $y2) {
                    $ids =  Point::whereBetween('game_started', [
                        date($y1 . '-01-01 00:00:00'),
                        date($y2 . '-12-31 23:59:59')
                    ])
                    ->pluck('player_id')->all();
                    $games = count($ids); // count all ids => games
                    $players = count(array_flip($ids)); // count unique ids => players
                    return ($games) ? round($games / $players) : 0;
                });
                $y = (($y2 - $y1) * 12) + ($month2/* - $month1*/);
                $y -= 2; // @INFO: 2012 10 months only - substracting 2 (WeC started 2012 March 9th)
                if ($total['games'] > 0) {
                    $score = number_format($total['points'] / ($y + $total['games'] + max(($avg_games - $total['games']), 0)), 2);
                }
            }

            $stats =
                [
                    'player_id' => $player->id,
                    'nickname' => $player->nickname,
                    'score' => $score,
                    'points' => $total['points'],
                    'games' => $total['games'],
                    'places' => $total['places'],
                    'avg_games' => $avg_games,
                    'pos' => 0, // placeholder
                ];
            if($nocache){
                $stats = [
                    'year' => $year,
                    'month' => $month,
                    'score' => $score,
                    'points' => $total['points'],
                    'games' => $total['games'],
                    'places' => $total['places'],
                    'avg_games' => $avg_games,
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
