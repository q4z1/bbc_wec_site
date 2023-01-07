<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ResultController;
use App\Http\Controllers\SeasonController;
use App\Models\Player;
use App\Models\PlayerAward;
use App\Models\Point;
use App\Models\Season;
use Illuminate\Http\Request;
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

    $res = new ResultController();
    $all_season = $res->all_player_stats($season);
    $all_alltime = $res->all_player_stats(); // default => alltime

    $stats_extra = Cache::remember('player.' . $player->id, now()->addHours(24), function () use ($player, $all_season, $all_alltime) {
        $pos_season = $pos_alltime = 1;

        foreach($all_season as $one){
          if($one['player']->id == $player->id) break;
          $pos_season++;
        }
        if($pos_season > count($all_season)) $pos_season = '';

        foreach($all_alltime as $one){
            if($one['player']->id == $player->id) break;
            $pos_alltime++;
        }
        if($pos_alltime > count($all_alltime)) $pos_alltime = '';

        $seasons = Season::orderBy('id', 'ASC')->pluck('id');

        return ['pos_season' => $pos_season, 'pos_alltime' => $pos_alltime, 'seasons' => $seasons];
    });

    $stats_season = $this->stats($player, $season);
    $stats_season['pos'] = $stats_extra['pos_season'];
    $stats_alltime = $this->stats($player); // default => alltime
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

      $total = Player::where('new', 0)->get()->count();


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

  public function stats(Player $player, $season=0) // 0 => alltime
  {
    return Cache::remember('player.' . $player->id . "_" . $season, now()->addHours(24), function () use ($player, $season) {
      $stats_col = Point::where('player_id', $player->id);
      if ($season > 0) {
        $date_range = SeasonController::dateRange($season);
        $stats_col = $stats_col->whereBetween('game_started', [
          $date_range['start'],
          $date_range['end']
        ]);
      }
      $stats_col = $stats_col->get();
      $total = ['points' => 0, 'games' => 0];
      foreach ($stats_col as $stat) {
        $total['points'] += $stat->points;
        $total['games'] += 1;
      }

      $stats =
        [
          'score' => number_format($this->calc_score($total['points'], $total['games']) / 1000, 2),
          'points' => $total['points'],
          'games' => $total['games'],
          'player' => $player,
          'season' => $season,
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
