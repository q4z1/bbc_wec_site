@extends('layouts.app')

@php
    // Der Controller hat pos1..pos10 bereits auf Nicknames (oder null) aufgeloest;
    // pos1 ist der Sieger.
    $standing = collect(range(1, 10))
        ->map(fn ($i) => $game->{"pos$i"})
        ->filter()
        ->values();
    $playedAt = $game->started
        ? \Illuminate\Support\Carbon::parse($game->started)->isoFormat('D MMM YYYY')
        : null;
    $metaTitle = 'Game #' . $game->number
        . ($game->pos1 ? ' – ' . $game->pos1 . ' wins' : '')
        . ' – ' . config('app.name');
    $metaDescription = \Illuminate\Support\Str::limit(trim(
        config('app.name') . ' game #' . $game->number
        . ($playedAt ? ', played ' . $playedAt : '')
        . ($game->pos1 ? '. Winner: ' . $game->pos1 : '')
        . ($standing->count() ? '. Players: ' . $standing->implode(', ') : '')
    ), 250);
@endphp

@section('title', $metaTitle)
@section('description', $metaDescription)
{{-- Einzelne Spielseiten tragen fuer sich genommen kaum eigenstaendigen Inhalt
     und existieren zu Tausenden; sie sollen crawlbar bleiben (follow), aber
     nicht im Index landen. Die Aggregatseiten bleiben indexierbar. --}}
@section('robots', 'noindex,follow')

@section('content')
<noscript>
    <h1>Game #{{ $game->number }} — {{ config('app.name') }}</h1>
    <p>
        @if($playedAt) Played {{ $playedAt }}. @endif
        @if($game->pos1) Winner: <strong>{{ $game->pos1 }}</strong>. @endif
        {{ $standing->count() }} players.
    </p>
    @if($standing->count())
        <p>Final standing:</p>
        <ol>
            @foreach($standing as $nick)
                <li><a href="{{ url('/player/' . rawurlencode($nick)) }}">{{ $nick }}</a></li>
            @endforeach
        </ol>
    @endif
    <p><a href="{{ route('results') }}">All game results</a></p>
</noscript>
<game-component :game="{{json_encode($game, true)}}"></game-component>
@endsection
