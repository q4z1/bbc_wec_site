@extends('layouts.app')

@section('content')
<player-component :pos_season="{{$pos_season}}" :pos_alltime="{{$pos_alltime}}" :player="{{json_encode($player, true)}}" :season="{{json_encode($season, true)}}" :stats="{{json_encode($stats, true)}}" :awards="{{json_encode($awards, true)}}"></player-component>
@endsection