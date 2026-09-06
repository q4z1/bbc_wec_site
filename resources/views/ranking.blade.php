@extends('layouts.app')

@section('title', 'Ranking – ' . config('app.name'))
@section('description', 'Season and all-time ranking of the ' . config('app.name') . ', the PokerTH tournament series.')

@section('content')
<ranking-component :results="{{json_encode($stats, true)}}" :season="{{ $season }}" :allseasons="{{json_encode($seasons, true)}}"></ranking-component>
@endsection
