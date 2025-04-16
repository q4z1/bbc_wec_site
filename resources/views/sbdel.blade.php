@extends('layouts.app')

@section('content')
<shoutbox-deleted-component :user="{{json_encode($user, true)}}"></shoutbox-deleted-component>
@endsection