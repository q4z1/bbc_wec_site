@extends('layouts.app')

@section('title', 'Players – ' . config('app.name'))
@section('description', 'All players of the ' . config('app.name') . ' with their game and ranking statistics.')

@section('content')
<players-component :players="{{json_encode($players, true)}}" :total="{{$total}}"></players-component>
@endsection
