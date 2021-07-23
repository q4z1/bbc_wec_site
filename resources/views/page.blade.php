@extends('layouts.app')

@section('content')
<page-component :page="{{json_encode($page, true)}}"></page-component>
@endsection
