@extends('layouts.app')

@section('content')
<h3>{{ env('APP_NAME') }}</h3>
<home-component markdown="{{ $markdown }}"></home-component>
@endsection