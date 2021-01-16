@extends('layouts.app')

@section('content')
<game-edit-component :game="{{json_encode($game, true)}}"></game-edit-component>
@endsection