<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function index(Request $request){
        $results = [
            [
                "id" => 1,
                "type" => "regular",
                "number" => 12234,
                "pos1" => "winner nickname",
                "pos2" => "2nd winner nick",
            ]
        ];
        return view('results', [
            "results" => $results
        ]);
    }
}
