@extends('layouts.app')

@section('content')
<ranking-component :stats="{{json_encode($stats, true)}}"></ranking-component>
@endsection
