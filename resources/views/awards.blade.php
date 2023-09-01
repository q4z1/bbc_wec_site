@extends('layouts.app')

@section('content')
<awards-component :awards="{{json_encode($awards, true)}}"></awards-component>
@endsection