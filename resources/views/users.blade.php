@extends('layouts.app')

@section('content')
<users-component :users="{{json_encode($users, true)}}"></users-component>
@endsection