@extends('layouts.app')

@section('content')
<home-component name="{{auth()->user()->name}}"></home-component>
@endsection
