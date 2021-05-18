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
            $year = ($year > 0) ? $year : date('Y');
            $games_alltime = Point::where('player_id', $player->id)->count();
            
            $stats_month = Point::where('player_id', $player->id)
                ->whereBetween('game_started', [
                    date($year . '-m-01 00:00:00', time()),
                    date($year . '-m-d H:i:s', time())
                ])
                ->get();
            $stats_year = Point::where('player_id', $player->id)
                ->whereBetween('game_started', [
                    date($year . '-01-01 00:00:00', time()),
                    date($year . '-m-d H:i:s', time())
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
            $avg_games_month = ['total' => 0, 'num' => 0];
            $all_games_month = DB::table('points')->selectRaw('player_id, count(*)')
                ->whereBetween('game_started', [
                    date($year . '-m-01 00:00:00', time()),
                    date($year . '-m-d H:i:s', time())
                ])
                ->groupBy('player_id')
                ->get();
            foreach($all_games_month as $pl){
                $avg_games_month['total'] += $pl->{'count(*)'};
                $avg_games_month['num'] += 1;
            }
            $avg_games_year = ['total' => 0, 'num' => 0];
            $all_games_year = DB::table('points')->selectRaw('player_id, count(*)')
                ->whereBetween('game_started', [
                    date($year . '-01-01 00:00:00', time()),
                    date($year . '-m-d H:i:s', time())
                ])
                ->groupBy('player_id')
                ->get();
            foreach($all_games_year as $pl){
                $avg_games_year['total'] += $pl->{'count(*)'};
                $avg_games_year['num'] += 1;
            }
            $avg_month = ($avg_games_month['num'] > 0) ? round($avg_games_month['total'] / $avg_games_month['num']) : 0;
            $avg_year = ($avg_games_year['num'] > 0) ? round($avg_games_year['total'] / $avg_games_year['num']) : 0;
            $stats =
            [
                'score_month' => number_format($stat_month['points'] / (1 + $stat_month['games'] + max(($avg_month - $stat_month['games']), 0)), 2),
                'score_year' => number_format($stat_year['points'] / (date("m") + $stat_year['games'] + max(($avg_year - $stat_year['games']), 0)), 2),
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
