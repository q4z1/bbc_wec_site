<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Action;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    if (auth()->user()->role !== 's' && auth()->user()->role !== 's') abort(401);
    return view('users', ['users' => User::get()]);
  }

  public function update(Request $request, User $user)
  {
    if (auth()->user()->role !== 's' && auth()->user()->role !== 'a') return ['success' => false, 'msg' => 'Unauthorized!'];
    $action = new Action();
    $action->action = "User " . $user->name . " updated.";
    $action->reason = $request->input('reason', "n/a");
    $action->user = Auth::id();
    $action->save();
    $user->role = $request->input('role', $user->role);
    $user->save();
    return ['success' => true, 'users' => User::get()];
  }

  public function delete(Request $request, User $user)
  {
    if (auth()->user()->role !== 's' && auth()->user()->role !== 'a') return ['success' => false, 'msg' => 'Unauthorized!'];
    $action = new Action();
    $action->action = "User " . $user->name . " deleted.";
    $action->reason = $request->input('reason', "n/a");
    $action->user = Auth::id();
    $action->save();
    $user->delete();
    return ['success' => true];
  }

  public function set_theme(Request $request)
  {
    if (auth()->user() && $request->input('v')) {
      $u = User::where('id', auth()->id())->first();
      $u->theme = $request->input('v');
      $u->save();
    }
  }
}
