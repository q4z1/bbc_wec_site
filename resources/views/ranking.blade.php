@extends('layouts.app')

@section('content')
<ranking-component :stats="{{json_encode($stats, true)}}" :stats_year="{{$year}}" :stats_month="{{$month}}"></ranking-component>
@endsection
