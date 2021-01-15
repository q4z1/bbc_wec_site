@extends('layouts.app')

@section('content')
<upload-game-component :last="{{json_encode($last, true)}}"></upload-game-component>
@endsection