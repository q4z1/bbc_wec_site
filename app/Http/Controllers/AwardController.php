<?php

namespace App\Http\Controllers;

use App\Models\Award;
use App\Models\Player;
use App\Models\PlayerAward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AwardController extends Controller
{
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
        $awards = Cache::rememberForever('awards', function () {
            return Award::get();
        });
        $players = Cache::rememberForever('players', function () {
            return Player::orderBy('nickname', 'ASC')->get();
        });
        return view('awards', [
            'awards' => $awards, 
            'players' => $players
        ]);
    }

    public function upload(Request $request){
        if(!$request->hasFile('award')) return ['success' => false, 'msg' => 'Uploaded File invalid.'];
        $path = str_replace('public', '/storage', $request->file('award')->store('public/awards'));
        $award = new Award();
        $award->filename = $path;
        $award->title = $request->title;
        $award->save();
        Cache::forget('awards');
        $awards = Cache::rememberForever('awards', function () {
            return Award::get();
        });
        return ['success' => true, 'msg' => 'Award uploaded.', 'awards' => $awards];
    }

    public function edit(Request $request, Award $award){
        if($request->hasFile('award')){
            Storage::delete('public/awards/' . basename($award->filename));
            $path = '/storage/awards/' . basename($request->file('award')->store('public/awards'));
            $award->filename = $path;
        }
        $award->title = $request->title;
        $award->save();
        Cache::forget('awards');
        $awards = Cache::rememberForever('awards', function () {
            return Award::get();
        });
        return ['success' => true, 'msg' => 'Award updated.', 'awards' => $awards];
    }

    public function assign(Request $request, Award $award){
        PlayerAward::where('award_id', $award->id)->delete();
        foreach($request->player as $player_id){
            $pa = new PlayerAward();
            $pa->award_id = $award->id;
            $pa->player_id = $player_id;
            $pa->save();
        }
        return ['success' => true];
    }


    public function assignments(Request $request, Award $award){
        return [
            'success' => true, 
            'assignments' => PlayerAward::where('award_id', $award->id)->with('player')->get()
            ->map(function ($a) {
                return $a->player;
            })
        ];
    }

    public function delete(Request $request, Award $award){
        Storage::delete('public/awards/' . basename($award->filename));
        PlayerAward::where('award_id', $award->id)->delete();
        $award->delete();
        Cache::forget('awards');
        $awards = Cache::rememberForever('awards', function () {
            return Award::get();
        });
        return ['success' => true, 'msg' => 'Award deleted.', 'awards' => $awards];
    }

}
