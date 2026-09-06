@extends('layouts.app')

@section('title', 'Game results – ' . config('app.name'))
@section('description', 'Every ' . config('app.name') . ' game: date, participants and final standing, filterable by season.')

@section('content')
<results-component :results="{{json_encode($results, true)}}" :totals="{{$totals}}" :season="{{$season}}" :allseasons="{{json_encode($seasons, true)}}"></results-component>
@endsection
