@extends('layouts.app')

@section('content')
<results-component :results="{{json_encode($results, true)}}" :totals="{{$totals}}" :season="{{$season}}" :allseasons="{{json_encode($seasons, true)}}"></results-component>
@endsection
