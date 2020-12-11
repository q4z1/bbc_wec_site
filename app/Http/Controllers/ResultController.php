<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Game;

class ResultController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index(Request $request){
        $results = DB::table('games')
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
                    'games.id', 'games.type', 'games.number', 'games.created_at',
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
                )
                ->get();

        return view('results', [
            "results" => $results
        ]);
    }

    public function filter(Request $request){
        dd('Filter');
    }
}
