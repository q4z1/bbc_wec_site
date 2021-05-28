@extends('layouts.app')

@section('content')
<game-upload-component :last="{{json_encode($last, true)}}"></game-upload-component>
@endsection