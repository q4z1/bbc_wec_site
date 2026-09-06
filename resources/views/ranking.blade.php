@extends('layouts.app')

@section('title', 'Ranking – ' . config('app.name'))
@section('description', 'Monthly and all-time ranking of the ' . config('app.name') . ', the PokerTH tournament series.')

@section('content')
<ranking-component :stats="{{json_encode($stats, true)}}" :stats_year="{{$year}}" :stats_month="{{$month}}"></ranking-component>
@endsection
