@extends('layouts.app')

@section('content')
<ranking-component :results="{{json_encode($stats, true)}}" :season="{{ $season }}" :allseasons="{{json_encode($seasons, true)}}"></ranking-component>
@endsection
