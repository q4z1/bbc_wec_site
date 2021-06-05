<?php

namespace App\Http\Controllers;

use App\Models\ShoutBoxMessage;
use Illuminate\Http\Request;

class ShoutBoxMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = (!is_null(auth())) ? auth()->user() : null;
        return view('shoutbox', ['user' => $user]);
    }

    public function post(Request $request)
    {
        $fp = $request->input('fp', null);
        $msg = $request->input('message', null);
        $nickname = $request->input('nickname', null);
        $user_id = (!is_null($request->user())) ? $request->user()->id : null;
        $active = (!is_null($request->user())) ? 2 : 1;
        $admin_post = $request->input('admin_post', 0);
        $post_active = $active;

        if(is_null($fp) || is_null($msg) || (is_null($nickname) && is_null($user_id))) return ['success' => false, 'msg' => 'Missing parameter!'];
        if(!is_null($user_id)){
            $nickname = $request->user()->name;
            $post_active = 2;
        }
        if(!is_null($request->user()) && in_array($request->user()->role, ['a', 's']) && intval($admin_post) === 1) $post_active = 3;

        $sbp = new ShoutBoxMessage();
        $sbp->user_id = $user_id;
        $sbp->nickname = $nickname;
        $sbp->fp = $fp;
        $sbp->ip = $request->ip;
        $sbp->message = $msg;
        $sbp->active = $post_active;
        $sbp->save();

        $posts = ShoutBoxMessage::where('active', '>=', $active)->limit(25)->orderBy('created_at', 'ASC')->with('player')->get();

        return ['success' => true, 'msg' => 'Message posted.', 'posts' => $posts];
    }

    public function delete(Request $request, ShoutBoxMessage $shoutBoxMessage)
    {
        $shoutBoxMessage->active = 0;
        $shoutBoxMessage->save();
        return ['success' => true, 'msg' => 'Message deleted.'];
    }

    public function filter(Request $request)
    {
        $offset = $request->input('offset', 0);
        $id = $request->input('mid', false);

        $post_active = 2;
        if(!is_null($request->user()) && in_array($request->user()->role, ['a', 's'])) $post_active = 3;

        if($id){
            $posts = ShoutBoxMessage::where('id', '<', $id - 4)->whereBetween('active', [1, $post_active])->orderBy('id', 'DESC')->limit(9)->with('player')->get();
            return ['success' => true, 'posts' => $posts];
        }

        $posts = ShoutBoxMessage::whereBetween('active', [1, $post_active])->orderBy('id', 'DESC')->limit(25)->offset($offset)->with('player')->get();

        return ['success' => true, 'posts' => $posts];
    }

    public function update(Request $request, ShoutBoxMessage $shoutBoxMessage)
    {
        $msg = $request->input('message', null);
        $admin_post = $request->input('admin_post', 0);
        if(is_null($request->user()) || $request->user()->id !== $shoutBoxMessage->user_id ) return ['success' => false, 'msg' => 'Unauthorized!'];
        elseif(is_null($msg)) return ['success' => false, 'msg' => 'Missing Parameter!'];
        $shoutBoxMessage->active = (in_array($request->user()->role, ['a', 's']) && $admin_post > 0) ? 3 : 2;
        $shoutBoxMessage->message = $msg;
        $shoutBoxMessage->save();
        return ['success' => true, 'msg' => 'Post updated.', 'post' => $shoutBoxMessage];
    }
}
