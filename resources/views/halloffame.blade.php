@extends('layouts.app')

@section('title', 'Hall of Fame – ' . config('app.name'))
@section('description', 'Hall of Fame of the ' . config('app.name') . ': title holders and record holders of the PokerTH tournament series.')

@section('content')
<halloffame-component :results="{{json_encode($results, true)}}"></halloffame-component>
@endsection
