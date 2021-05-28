@extends('layouts.app')

@section('content')
<registration-component :gamedates="{{json_encode($gamedates, true)}}" :calstart="{{ json_encode($calstart, true) }}"></registration-component>
@endsection