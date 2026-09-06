@extends('layouts.app')

@section('title', 'Game results – ' . config('app.name'))
@section('description', 'Every ' . config('app.name') . ' game: date, participants and final standing.')

@section('content')
<results-component :results="{{json_encode($results, true)}}" :totals="{{$totals}}"></results-component>
@endsection
