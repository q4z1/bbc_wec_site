@extends('layouts.app')

@section('content')
<assign-award-component :awards="{{json_encode($awards, true)}}" :players="{{json_encode($players, true)}}"></assign-award-component>
@endsection