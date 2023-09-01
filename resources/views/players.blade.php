@extends('layouts.app')

@section('content')
<players-component :players="{{json_encode($players, true)}}" :total="{{$total}}"></players-component>
@endsection
