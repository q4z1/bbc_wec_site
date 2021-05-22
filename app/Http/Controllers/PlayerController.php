<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Point;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

        $awards = [];

        return view('player', [
            "player" => $player,
            "stats" => $this->stats($player, date('Y')),
            'awards' => $awards
        ]);
    }

    public function stats(Player $player, $year){
        return Cache::remember('player.' . $player->id . "_" . $year, now()->addHours(24), function() use($player, $year){
            $year1 = ($year > 0) ? $year : 2012;
            $year2 = ($year > 0) ? $year : date('Y');
            $games_alltime = Point::where('player_id', $player->id)->count();
            
            $stats_month = Point::where('player_id', $player->id)
                ->whereBetween('game_started', [
                    date($year2 . '-m-01 00:00:00', time()),
                    date($year2 . '-m-d H:i:s', time())
                ])
                ->get();
            $stats_year = Point::where('player_id', $player->id)
                ->whereBetween('game_started', [
                    date($year1 . '-01-01 00:00:00'),
                    date($year2 . '-12-31 23:59:59')
                ])
                ->get();
            $stat_month = $stat_year = ['points' => 0, 'games' => 0];
            foreach($stats_month as $stat){
                $stat_month['points'] += $stat->points;
                $stat_month['games'] += 1;
            }
            foreach($stats_year as $stat){
                $stat_year['points'] += $stat->points;
                $stat_year['games'] += 1;
            }
            $all_month = ['total' => 0, 'num' => 0];
            $all_games_month = DB::table('points')->selectRaw('player_id, count(*)')
                ->whereBetween('game_started', [
                    date($year2 . '-m-01 00:00:00', time()),
                    date($year2 . '-m-31 23:59:59', time())
                ])
                ->groupBy('player_id')
                ->get();
            foreach($all_games_month as $pl){
                $all_month['total'] += $pl->{'count(*)'};
                $all_month['num'] += 1;
            }
            $all_year = ['total' => 0, 'num' => 0];
            $all_games_year = DB::table('points')->selectRaw('player_id, count(*)')
                ->whereBetween('game_started', [
                    date($year1 . '-01-01 00:00:00', time()),
                    date($year2 . '-12-31 23:59:59', time())
                ])
                ->groupBy('player_id')
                ->get();
            foreach($all_games_year as $pl){
                $all_year['total'] += $pl->{'count(*)'};
                $all_year['num'] += 1;
            }
            $avg_month = ($all_month['num'] > 0) ? round($all_month['total'] / $all_month['num']) : 0;
            $avg_year = ($all_year['num'] > 0) ? round($all_year['total'] / $all_year['num']) : 0;
            $m = ($year2 != date('Y')) ? 12 : date('m');
            if($year1 == 2012){
                $ts1 = strtotime('2012-09-03');
                $ts2 = strtotime(date('Y-m-d'));

                $y1 = date('Y', $ts1);
                $y2 = date('Y', $ts2);

                $month1 = date('m', $ts1);
                $month2 = date('m', $ts2);

                $m = (($y2 - $y1) * 12) + ($month2 - $month1);
            }
            $stats =
            [
                'score_month' => number_format($stat_month['points'] / (1 + $stat_month['games'] + max(($avg_month - $stat_month['games']), 0)), 2),
                'score_year' => number_format($stat_year['points'] / ($m + $stat_year['games'] + max(($avg_year - $stat_year['games']), 0)), 2),
                'month' => $stat_month,
                'year' => $stat_year,
                'avg_games_month' => $avg_month,
                'avg_games_year' => $avg_year,
                'games_alltime' => $games_alltime,
                'player' => $player
            ];
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
