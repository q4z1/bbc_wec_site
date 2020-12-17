@extends('layouts.app')

@section('content')
<halloffame-component :results="{{json_encode($results, true)}}"></halloffame-component>
@endsection
