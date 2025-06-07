<?php

namespace App\Http\Controllers;

use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BotFileController extends Controller
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
    public function show_files()
    {
      $items = scandir(public_path() . "/exp3/bbcbot");
      $files = [];
      $files[] = "Please select a Botfile";
      foreach($items as $item) {
      
          // Ignore the . and .. folders
          if($item != "." && $item != "..") {
              if (is_file(public_path() . "/exp3/bbcbot/" . $item)) {
                  // this is the file
                  $files[] = $item;
              }
          }
      }
      return view('botfiles', ['files' => $files]);

    }

    public function update_file(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        if(is_null($payload) || !is_array($payload) || !array_key_exists("file", $payload) || !array_key_exists("content", $payload)){
          return ["status" => false, 'msg' => "Parameter missing!"];
        }
        $file = $payload["file"];
        if (!is_file(public_path() . "/exp3/bbcbot/" . $file)) return ["status" => true, 'msg' => "File not found - not saved!"];
        $content = $payload["content"];
        if($content == "") return ["status" => false, 'msg' => "File is empty - not saved!"];
        file_put_contents(public_path() . "/exp3/bbcbot/" . $file, $content);
        $action = new Action();
        $action->action = "Botfile " . $file . " updated.";
        $action->reason = $request->input('reason', "n/a");
        $action->user = Auth::id();
        $action->save();
        return ["status" => true, 'msg' => "BotFile has been saved."];
    }

    public function get_file(Request $request)
    {
      $payload = json_decode($request->getContent(), true);

      if(is_null($payload) || !is_array($payload) || !array_key_exists("file", $payload)) return ["status" => false, 'msg' => "Parameter missing!"];
      $file = $payload["file"];
      if (is_file(public_path() . "/exp3/bbcbot/" . $file)) {
        $status = true;
        $msg = file_get_contents(public_path() . "/exp3/bbcbot/" . $file);
      }
      else{
        $status = false;
        $msg = "File not found!";
      }
       
      return ["status" => $status, 'msg' => $msg];
    }
}
