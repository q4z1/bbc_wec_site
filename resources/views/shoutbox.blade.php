@extends('layouts.app')

@section('content')
<shoutbox-component :user="{{json_encode($user, true)}}"></shoutbox-component>
@endsection
