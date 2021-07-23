<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

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
    if (!$page_id) {
      $page = new Page();
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
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Page  $page
   * @return \Illuminate\Http\Response
   */
  public function edit(Request $request, $page)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Page  $page
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $page)
  {
    //
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Page  $page
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request, Page $page)
  {
    if ($page->slug === "home") return ['success' => false, 'msg' => 'Home cannot be deleted!'];
    $page->delete();
    return ['success' => true];
  }
}
