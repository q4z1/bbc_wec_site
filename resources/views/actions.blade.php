@extends('layouts.app')

@section('content')
<actions-component :actions="{{json_encode($actions, true)}}" :totals="{{$totals}}"></actions-component>
@endsection
