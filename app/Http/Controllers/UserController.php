<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function set_theme(Request $request){
        if(auth()->user() && $request->input('v')){
            $u = User::where('id', auth()->id())->first();
            $u->theme = $request->input('v');
            $u->save();
        }
    }

    public function set_role(Request $request){
        if(auth()->user() && $request->input('r')){
            $u = User::where('id', auth()->id())->first();
            $u->role = $request->input('r');
            $u->save();
        }
    }
}
