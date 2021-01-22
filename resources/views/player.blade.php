@extends('layouts.app')

@section('content')
<player-component :player="{{json_encode($player, true)}}" :stats="{{json_encode($stats, true)}}"></player-component>
@endsection