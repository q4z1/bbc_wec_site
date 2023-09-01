<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    $page = Page::where('slug', 'home')->first();
    $markdown = ($page) ? $page->markdown : 'No content!';
    return view('home', ['markdown' => $markdown]);
  }
}
