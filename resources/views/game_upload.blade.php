@extends('layouts.app')

@section('content')
<game-upload-component :last="{{json_encode($last, true)}}"></upload-game-component>
@endsection