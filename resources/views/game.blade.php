@extends('layouts.app')

@section('content')
<game-component :game="{{json_encode($game, true)}}"></game-component>
@endsection