<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResultController;
use App\Http\Controllers\SeasonController;
use App\Models\Player;
use App\Models\PlayerAward;
use App\Models\Point;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth', ['only' => ['delete', 'tickets']]);
  }

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

    $season = Season::orderBy('start', 'DESC')->first()->id;

    $stats_extra = Cache::remember('player.' . $player->id, now()->addHours(24), function () use ($player, $season) {
        $pos_season = $pos_alltime = 1;
        $res = new ResultController();

        $all = $res->all_player_stats($season);
        foreach($all as $one){
          if($one['player_id'] == $player->id) break;
          $pos_season++;
        }
        if($pos_season > count($all)) $pos_season = '';

        $all = $res->all_player_stats(); // default => alltime
        foreach($all as $one){
            if($one['player_id'] == $player->id) break;
            $pos_alltime++;
        }
        if($pos_alltime > count($all)) $pos_alltime = '';

        $seasons = Season::orderBy('id', 'ASC')->pluck('id');

        return ['pos_season' => $pos_season, 'pos_alltime' => $pos_alltime, 'seasons' => $seasons];
    });

    $stats_season = $this->stats($player, $season, true);
    $stats_season['pos'] = $stats_extra['pos_season'];
    $stats_alltime = $this->stats($player, 0, true);
    $stats_alltime['pos'] = $stats_extra['pos_alltime'];

    return view('player', [
      "player" => $player,
      "season" => $season,
      'stats' => [
          'season' => $stats_season,
          'alltime' => $stats_alltime,
          'seasons' => $stats_extra['seasons'],
      ],
      'awards' => $awards
    ]);
  }

  public function all(Request $request)
  {
    if ($request->method() == 'GET') {
      return view('players');
    } else {
      $filters = $request->input('filters');
      $page = $request->input('page', 1);
      $pagesize = $request->input('pageSize', 50);
      $sort = $request->input('sort');

      $total = Player::where('new', 0)->count();

      $query = Player::where('new', 0)->orderBy($sort['prop'], (($sort['order'] == 'descending') ? 'DESC' : 'ASC'))
        ->offset(($page - 1) * $pagesize)->limit($pagesize);
      if (!empty($filters)) {
        $query->where('nickname', 'LIKE', $filters['value'] . '%');
      }

      $players = $query->get()->map(function ($player) {
        return $player;
      });

      return ['total' => $total, 'data' => $players];
    }
  }

  public function playerlist(Request $request)
  {
    $res = DB::table('players')
      ->select(
        'players.id',
        'players.nickname'
      )->orderBy('nickname', 'ASC')
      ->get();
    return $res;
  }

  public function tickets(Request $request, Player $player)
  {
    $success = false;
    if ($request->exists('s2') && $request->exists('s3') && $request->exists('s4')) {
      $player->s2_tickets = $request->s2;
      $player->s3_tickets = $request->s3;
      $player->s4_tickets = $request->s4;
      $player->save();
      $success = true;
    }
    return ['success' => $success];
  }

  private function calculateTotals(Collection $collection)
  {
    $plucked = $collection->pluck('points');
    $points = $plucked->sum();
    $games = $plucked->count();
    $places = [];
    if($games){
      $collection = $collection->groupBy('type');
      for($i=1;$i<=4;$i++){
        $step = $collection->get($i);
        if($step){
          $plucked = $step->pluck('pos')->countBy()->all();
          $step = [];
          for($j=1;$j<=10;$j++){
            $step[] = array_key_exists($j, $plucked) ? $plucked[$j] : 0;
          }
        }
        $places[] = $step;
      }
    }
    return ['points' => $points, 'games' => $games, 'step1' => 0, 'places' => $places ];
  }

  public function stats(Player $player, $season=0, $places=false)
  {
    $places = ($places) ? 1 : 0;
    return Cache::remember('player.' . $player->id . '_' . $season . '_' . $places, now()->addHours(24), function () use ($player, $season, $places) {
      $points = Point::where('player_id', $player->id);
      if ($season > 0) { // 0 => alltime
        $date_range = SeasonController::dateRange($season);
        $points = $points->whereBetween('game_started', [
          $date_range['start'],
          $date_range['end']
        ]);
      }
      $total = ['points' => 0, 'games' => 0, 'step1' => 0, 'places' => [] ];
      if($places){
        $points = $points->select('points', 'pos', 'type')->get();
        $total = $this->calculateTotals($points);
      }else{
        if($season >= 9) $points = $points->select('points', 'type')->get();
        $plucked = $points->pluck('points');
        $total['points'] = $plucked->sum();
        $total['games'] = $plucked->count();
        if($season >= 9) $total['step1'] = $points->where('type', 1)->count();
      }

      $stats =
        [
          'player_id' => $player->id,
          'nickname' => $player->nickname,
          'score' => number_format($this->calc_score($total['points'], $total['games']) / 1000, 2),
          'points' => $total['points'],
          'games' => $total['games'],
          'step1' => $total['step1'],
          'places' => $total['places'],
          'pos' => 0, // placeholder
        ];
      return $stats;
    });
  }

  public function calc_score($points, $games)
  {
    if ($games <= 0 or $points <= 0) return 0;
    $coefficient = 1 + log((float)$games, 2); //logarithm with base 2
    $score = (float)$points * $coefficient / (float)$games;
    return (int)($score * 1000);
  }

  public function delete(Request $request, Player $player)
  {
    $player->delete();
    return ['success' => true];
  }
}
