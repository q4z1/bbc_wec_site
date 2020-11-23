<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Player;
use App\Http\Controllers\LogFileController;
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
        $g->save();
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
        return view('upload.game');
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
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function show(Game $game)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function edit(Game $game)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Game  $game
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Game $game)
    {
        //
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
