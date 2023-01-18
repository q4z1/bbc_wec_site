@extends('layouts.app')

@section('content')
<player-component :player="{{json_encode($player, true)}}" :season="{{json_encode($season, true)}}" :stats="{{json_encode($stats, true)}}" :awards="{{json_encode($awards, true)}}"></player-component>
@endsection