@extends('layouts.app')

@section('content')
<player-component :player="{{json_encode($player, true)}}" :awards="{{json_encode($awards, true)}}" :stats="{{json_encode($stats, true)}}"></player-component>
@endsection