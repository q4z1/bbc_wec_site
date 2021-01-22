@extends('layouts.app')

@section('content')
<ranking-component :results="{{json_encode($results, true)}}"></ranking-component>
@endsection
