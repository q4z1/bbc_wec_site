@extends('layouts.app')

@section('content')
<awards-component :awards="{{json_encode($awards, true)}}" :players="{{json_encode($players, true)}}"></awards-component>
@endsection