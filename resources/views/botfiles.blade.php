@extends('layouts.app')

@section('content')
<bot-files-component :files="{{json_encode($files, true)}}"></bot-file-component>
@endsection