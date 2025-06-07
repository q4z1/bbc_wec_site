<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PageController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth', ['except' => ['show']]);
  }

  public function index(Request $request)
  {
    abort(404);
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function get(Request $request)
  {
    return view('pages', ['pages' => Page::get()]);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create(Request $request)
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $page_id = $request->input('id', false);
    $slug = $request->input('slug', '');
    if($slug === '') return ['status' => false, 'msg' => 'Slug may not be empty.'];
    $act = "edited";
    if (!$page_id) {
      $page = new Page();
      $act = "created";
    } else {
      $page = Page::find($page_id);
    }
    $pnum = Page::where('slug', $slug)->count();
    if ($pnum > 0 && (!$page_id || $page->slug != $slug)) return ['status' => false, 'msg' => 'Slug already exists.'];
    $page->title = $request->input('title', '');
    $page->slug = $slug;
    $page->markdown = $request->input('markdown', 0);
    $page->order = $request->input('order', 0);
    $page->active = $request->input('active', 0);
    $page->save();
    $action = new Action();
    $action->action = "Page " . $page->slug . " " . $act . "."; 
    $action->reason = $request->input('reason', "n/a");
    $action->user = Auth::id();
    $action->save();
    return ['status' => true, 'msg' => 'Page saved.'];
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Page  $page
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request, $page)
  {
    $page = Page::where('slug', $page)->first();
    abort_if(!$page, 404);
    return view('page', ['page' => $page]);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Page  $page
   * @return \Illuminate\Http\Response
   */
  public function delete(Request $request, Page $page)
  {
    if ($page->slug === "home") return ['success' => false, 'msg' => 'Home cannot be deleted!'];
    $action = new Action();
    $action->action = "Page " . $page->slug . " deleted."; 
    $action->reason = $request->input('reason', "n/a");
    $action->user = Auth::id();
    $action->save();
    $page->delete();
    return ['status' => true];
  }
}
