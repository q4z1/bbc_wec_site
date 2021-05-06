<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Game;
use App\Models\Player;
use App\Models\Point;
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
        $stats = Point::where('player_id', $player->id)->with(
            [
                'game',
                'game.pos1',
                'game.pos2',
                'game.pos3',
                'game.pos4',
                'game.pos5',
                'game.pos6',
                'game.pos7',
                'game.pos8',
                'game.pos9',
                'game.pos10'
            ]
        )->get();
        return view('player', [
            "player" => $player,
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

            $total = Player::get()->count();

    
            $query = Player::orderBy($sort['prop'], (($sort['order'] == 'descending') ? 'DESC' : 'ASC'))
            ->offset(($page-1)*$pagesize)->limit($pagesize);
            if(!empty($filters)){
                $query->where('nickname', 'LIKE', $filters['value'] . '%');
            }
            $players = $query->get()->map(function($player){
                // $player->final_score = number_format((float)($player->final_score / 100), 2, '.', '');
                // $player->average_score = number_format((float)($player->average_score / 100), 2, '.', '');
                return $player;
            });

            return ['total' => $total, 'data' => $players];
        }

    }

    public function playerlist(Request $request){
        $res = DB::table('players')
        ->select(
            'players.id', 'players.nickname'
        )->orderBy('id', 'ASC')
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

    public function delete(Request $request, Player $player){
        $player->delete();
        return ['success' => true];
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
