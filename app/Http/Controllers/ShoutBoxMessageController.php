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
        return view('shoutbox');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShoutBoxMessage  $shoutBoxMessage
     * @return \Illuminate\Http\Response
     */
    public function show(ShoutBoxMessage $shoutBoxMessage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ShoutBoxMessage  $shoutBoxMessage
     * @return \Illuminate\Http\Response
     */
    public function edit(ShoutBoxMessage $shoutBoxMessage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ShoutBoxMessage  $shoutBoxMessage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ShoutBoxMessage $shoutBoxMessage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShoutBoxMessage  $shoutBoxMessage
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShoutBoxMessage $shoutBoxMessage)
    {
        //
    }
}
