@extends('layouts.app')

@section('content')
<results-component :results="{{json_encode($results, true)}}"></results-component>
@endsection
