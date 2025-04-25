<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActionController extends Controller
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
      $totals = DB::table('actions')
      ->select(
          '*'
      )
      ->count();
      $actions = DB::table('actions')
      ->select(
          'actions.created_at', 'actions.action', 'actions.user', 'actions.reason'
      )->orderBy('id', 'DESC')
      ->limit(10)
      ->get()->map(function ($a) {
        $a->user = User::where('id', '=', $a->user)->first()->name;
        return $a;
      });

      $params = [
        "actions" => $actions,
        "totals" => $totals
      ];
      return view('actions', $params);
    }

    public function filter(Request $request){
      $page = $request->input('page', 1);
      $actions = DB::table('actions')
      ->select(
          'actions.created_at', 'actions.action', 'actions.user', 'actions.reason'
      )->orderBy('id', 'DESC');
      $total = $actions->count();
      $actions = $actions->offset(($page-1)*10)
      ->take(10)
      ->get()->map(function ($a) {
        $a->user = User::where('id', '=', $a->user)->first()->name;
        return $a;
      });
      return ['success' => true, 'result' => $actions, 'total' => $total];
    }
}
