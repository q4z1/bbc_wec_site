<?php

namespace App\Http\Controllers;

use App\Models\ShoutBoxMessage;
use App\Models\Action;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShoutBoxMessageController extends Controller
{
    protected $ascii_emoji;

    public function __construct(){
        $this->ascii_emoji = [
            ' o/' => ' 👋',
            ' </3' => ' 💔',
            ' <3' => ' 💗',
            ' 8-D' => ' 😁',
            ' 8D' => ' 😁',
            ' :-D' => ' 😁',
            ' =-3' => ' 😁',
            ' =-D' => ' 😁',
            ' =3' => ' 😁',
            ' =D' => ' 😁',
            ' B^D' => ' 😁',
            ' X-D' => ' 😁',
            ' XD' => ' 😁',
            ' x-D' => ' 😁',
            ' xD' => ' 😁',
            ' :\')' => ' 😂',
            ' :\'-)' => ' 😂',
            ' :-))' => ' 😃',
            ' 8)' => ' 😄',
            ' :)' => ' 😄',
            ' :-)' => ' 😄',
            ' :3' => ' 😄',
            ' :D' => ' 😄',
            ' :]' => ' 😄',
            ' :^)' => ' 😄',
            ' :c)' => ' 😄',
            ' :o)' => ' 😄',
            ' :}' => ' 😄',
            ' :っ)' => ' 😄',
            ' =)' => ' 😄',
            ' =]' => ' 😄',
            ' 0:)' => ' 😇',
            ' 0:-)' => ' 😇',
            ' 0:-3' => ' 😇',
            ' 0:3' => ' 😇',
            ' 0;^)' => ' 😇',
            ' O:-)' => ' 😇',
            ' 3:)' => ' 😈',
            ' 3:-)' => ' 😈',
            ' }:)' => ' 😈',
            ' }:-)' => ' 😈',
            ' *)' => ' 😉',
            ' *-)' => ' 😉',
            ' :-,' => ' 😉',
            ' ;)' => ' 😉',
            ' ;-)' => ' 😉',
            ' ;-]' => ' 😉',
            ' ;D' => ' 😉',
            ' ;]' => ' 😉',
            ' ;^)' => ' 😉',
            ' :-|' => ' 😐',
            ' :|' => ' 😐',
            ' :(' => ' 😒',
            ' :-(' => ' 😒',
            ' :-<' => ' 😒',
            ' :-[' => ' 😒',
            ' :-c' => ' 😒',
            ' :<' => ' 😒',
            ' :[' => ' 😒',
            ' :c' => ' 😒',
            ' :{' => ' 😒',
            ' :っC' => ' 😒',
            ' %)' => ' 😖',
            ' %-)' => ' 😖',
            ' :-P' => ' 😜',
            ' :-b' => ' 😜',
            ' :-p' => ' 😜',
            ' :-Þ' => ' 😜',
            ' :-þ' => ' 😜',
            ' :P' => ' 😜',
            ' :b' => ' 😜',
            ' :p' => ' 😜',
            ' :Þ' => ' 😜',
            ' :þ' => ' 😜',
            ' ;(' => ' 😜',
            ' =p' => ' 😜',
            ' d:' => ' 😜',
            ' :-||' => ' 😠',
            ' :@' => ' 😠',
            ' :-.' => ' 😡',
            ' :-/' => ' 😡',
            ' :/' => ' 😡',
            ' :L' => ' 😡',
            ' :S' => ' 😡',
            ' :\\' => ' 😡',
            ' =/' => ' 😡',
            ' =L' => ' 😡',
            ' =\\' => ' 😡',
            ' :\'(' => ' 😢',
            ' :\'-(' => ' 😢',
            ' ^5' => ' 😤',
            ' ^<_<' => ' 😤',
            ' o/\\o' => ' 😤',
            ' |-O' => ' 😫',
            ' |;-)' => ' 😫',
            ' :###..' => ' 😰',
            ' :-###..' => ' 😰',
            ' D-\':' => ' 😱',
            ' D8' => ' 😱',
            ' D:' => ' 😱',
            ' D:<' => ' 😱',
            ' D;' => ' 😱',
            ' D=' => ' 😱',
            ' DX' => ' 😱',
            ' v.v' => ' 😱',
            ' 8-0' => ' 😲',
            ' :-O' => ' 😲',
            ' :-o' => ' 😲',
            ' :O' => ' 😲',
            ' :o' => ' 😲',
            ' O-O' => ' 😲',
            ' O_O' => ' 😲',
            ' O_o' => ' 😲',
            ' o-o' => ' 😲',
            ' o_O' => ' 😲',
            ' o_o' => ' 😲',
            ' :$' => ' 😳',
            ' #-)' => ' 😵',
            ' :#' => ' 😶',
            ' :&' => ' 😶',
            ' :-#' => ' 😶',
            ' :-&' => ' 😶',
            ' :-X' => ' 😶',
            ' :X' => ' 😶',
            ' :-J' => ' 😼',
            ' :*' => ' 😽',
            ' :^*' => ' 😽',
            ' ಠ_ಠ' => ' 🙅',
            ' *\\0/*' => ' 🙆',
            ' \\o/' => ' 🙆',
            ' :>' => ' 😄',
            ' >.<' => ' 😡',
            ' >:(' => ' 😠',
            ' >:)' => ' 😈',
            ' >:-)' => ' 😈',
            ' >:/' => ' 😡',
            ' >:O' => ' 😲',
            ' >:P' => ' 😜',
            ' >:[' => ' 😒',
            ' >:\\' => ' 😡',
            ' >;)' => ' 😈',
            ' >_>^' => ' 😤',
        ];
    }

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

        if(is_null($fp) || is_null($msg) || (is_null($nickname) && is_null($user_id))) return ['success' => false, 'msg' => ' Missing parameter!'];
        if(!is_null($user_id)){
            $nickname = $request->user()->name;
            $post_active = 2;
        }

        if($fp == "7ea406519a2c2f6b5943e7822cdabb4f"
         || $fp == "d0055250f14a24da48b71e046a21bd14"
         || $fp == "0fd772a0c8088640f94a4e4229e00095"
         || $fp == "c15fca655ad793d947b2478d62e74649"
         || $fp == "18bf9147080862f874a346d8be5a82ab"
        //  || $fp == "9b0b8c9abfc2292a1c1326d753c65937" // sweets
        ){
          $post_active = 0;
        }
        if(!is_null($request->user()) && in_array($request->user()->role, ['a', 's']) && intval($admin_post) === 1) $post_active = 3;

        $sbp = new ShoutBoxMessage();
        $sbp->user_id = $user_id;
        $sbp->nickname = $nickname;
        $sbp->fp = $fp;
        $sbp->ip = $request->ip();
        $sbp->message = strip_tags($msg);
        $sbp->active = $post_active;
        $sbp->save();

        $posts = ShoutBoxMessage::where('active', '>=', $active)->limit(200)->orderBy('created_at', 'ASC')->with('player')->get();

        return ['success' => true, 'msg' => 'Message posted.', 'posts' => $posts];
    }

    public function delete(Request $request, ShoutBoxMessage $shoutBoxMessage)
    {
        $shoutBoxMessage->active = 0;
        $shoutBoxMessage->save();
        $action = new Action();
        $action->action = "Shoutbox Message #" . $shoutBoxMessage->id . " deleted.";
        $action->reason = "n/a"; // @TODO: reason handling
        $action->user = Auth::id();
        $action->save();
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

        $posts = ShoutBoxMessage::whereBetween('active', [1, $post_active])->orderBy('id', 'DESC')->limit(200)->offset($offset)->with('player')->get();
        
        // $pposts = []
        // foreach($posts as $post){

        // }
        $posts = $this->map($posts);

        return ['success' => true, 'posts' => $posts];
    }

    public function update(Request $request, ShoutBoxMessage $shoutBoxMessage)
    {
        $msg = $request->input('message', null);
        $admin_post = $request->input('admin_post', 0);
        if(is_null($request->user()) || $request->user()->id !== $shoutBoxMessage->user_id ) return ['success' => false, 'msg' => 'Unauthorized!'];
        elseif(is_null($msg)) return ['success' => false, 'msg' => 'Missing Parameter!'];
        $shoutBoxMessage->active = (in_array($request->user()->role, ['a', 's']) && $admin_post > 0) ? 3 : 2;
        $shoutBoxMessage->message = strip_tags($msg);
        $shoutBoxMessage->save();
        // $action = new Action();
        // $action->action = "Shoutbox Message #" . $shoutBoxMessage->id . " updated.";
        // $action->reason = "n/a"; // @TODO: reason handling
        // $action->user = Auth::id();
        // $action->save();
        return ['success' => true, 'msg' => 'Post updated.', 'post' => $shoutBoxMessage];
    }

    private function map($posts){
        return $posts->map(function($p){
            // 1. urls
            // $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
            // $p->message = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $p->message);
            $p->message = nl2br(str_replace(array_keys($this->ascii_emoji), $this->ascii_emoji, $p->message));
            return $p;
        });
    }


    public function map_single(Request $request, ShoutBoxMessage $sbpost){
        return ['success' => true, 'post' => $this->map(collect([$sbpost]))->first()];
    }

    public function deleted_view()
    {
        $user = (!is_null(auth())) ? auth()->user() : null;
        if(is_null($user) || !in_array($user->role, ['a', 's'])) return view('shoutbox', ['user' => $user]);
        return view('sbdel', ['user' => $user]);
        
    }

    public function deleted_action(Request $request)
    {
      $user = (!is_null(auth())) ? auth()->user() : null;
      if(is_null($user) || !in_array($user->role, ['a', 's'])) return [ "success" => false, "reason" => "Not allowed!"];
      $msg = ShoutBoxMessage::where("id", "=", $request->input('sbmsg', 0))->first();
      if($msg === null) return [ "success" => false, "reason" => "SB-Message not found!"];
      $msg->active = 1;
      $msg->save();
      $reason = $request->input('reason', "n/a");
      $action = new Action();
      $action->action = "Shoutbox Message #" . $msg->id . " undeleted.";
      $action->reason = $reason; // @TODO: reason handling
      $action->user = Auth::id();
      $action->save();
      return [ "success" => true];
    }

    public function deleted_filter(Request $request)
    {
      $user = (!is_null(auth())) ? auth()->user() : null;
      if(is_null($user) || !in_array($user->role, ['a', 's'])) return [ "success" => false, "reason" => "Not allowed!"];
      $page = $request->input('page', 1);
      $pagesize = $request->input('pageSize', 50);
      $sort = $request->input('sort', null);

      $new = (!empty($filters) && $filters['new']) ? 1 : 0;
      $total = ShoutBoxMessage::where('active', "=", 0)->count();

      $query = ShoutBoxMessage::where('active', "=", 0);
      // if(!is_null($sort)) $query->orderBy(
      //   $sort['prop'], (($sort['order'] == 'descending') ? 'DESC' : 'ASC')
      // );
      $query->orderBy("id", "DESC");
      $query->offset(($page - 1) * $pagesize)->limit($pagesize);

      $posts = $query->get()->map(function ($post) use ($user) {
        $p = [];
        $p["id"] = $post->id;
        $p["fp"] = $post->fp;
        $p["ip"] = $post->ip;
        if($user->role == 's') $p["ip"] = $post->ip;
        $p["nickname"] = $post->nickname;
        $p["message"] = $post->message;
        $p["created_at"] = $post->created_at->toDateTimeString();
        return $p;
      });

      return ['total' => $total, 'data' => $posts, "success" => true];
    }
}
