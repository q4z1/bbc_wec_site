@extends('layouts.app')

@section('content')
<home-component markdown="{{ $markdown }}"></home-component>
@endsection