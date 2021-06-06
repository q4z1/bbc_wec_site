<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(){
        return view('users', ['users' => User::get()]);
    }

    public function update(Request $request, User $user){
        $user->role = $request->input('role', $user->role);
        $user->save();
        return ['success' => true, 'users' => User::get()];
    }

    public function delete(Request $request, User $user){
        $user->delete();
        return ['success' => true];
    }

    public function set_theme(Request $request){
        if(auth()->user() && $request->input('v')){
            $u = User::where('id', auth()->id())->first();
            $u->theme = $request->input('v');
            $u->save();
        }
    }
}
