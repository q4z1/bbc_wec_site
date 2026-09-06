@extends('layouts.app')

@section('title', $player->nickname . ' – ' . config('app.name'))
@section('description', $player->nickname . ' at the ' . config('app.name') . ': games played, ranking position, awards and statistics.')

@section('content')
<player-component :player="{{json_encode($player, true)}}" :season="{{json_encode($season, true)}}" :stats="{{json_encode($stats, true)}}" :awards="{{json_encode($awards, true)}}"></player-component>
@endsection
