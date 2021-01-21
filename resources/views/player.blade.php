@extends('layouts.app')

@section('content')
<player-component :player="{{json_encode($player, true)}}"></player-component>
@endsection