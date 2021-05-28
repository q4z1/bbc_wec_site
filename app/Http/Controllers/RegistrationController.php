<?php

namespace App\Http\Controllers;

use App\Models\GameDate;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{

    public function index(Request $request)
    {
        return view('registration', [
            'gamedates' => $this->getUpcomingGames(), 'calstart' => date("Y-m-01", strtotime('-1 month'))]);
    }

    public function add(Request $request)
    {
        //
    }

    public function update(Request $request, Registration $reg)
    {
        //
    }

    public function delete(Registration $reg)
    {
        //
    }

    public function register(Request $request, GameDate $date)
    {
        //
    }

    public function getUpcomingGames(){
        $dates = GameDate::where('date','>=', date("Y-m-01 00:00:00", strtotime('-1 month')))->with('regs.player')->get()->map(function($date){
            foreach($date->regs as $i => $reg){
                $p = $reg->player;
                $u = User::where('name', $p->nickname)->first();
                $date->regs[$i]->player->admin = ($u && in_array($u->role, ['a', 's'])) ? true : false;
            }
            return $date;
        });
        return $dates;
    }
}
