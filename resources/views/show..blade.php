@extends('layouts.app')

@section('content')
    <h1>{{ $liste->nom }}</h1>
    <h2>Groupes</h2>
    <ul>
        @foreach ($groupes as $groupe)
            <li>{{ $groupe->nom }}</li>
        @endforeach
    </ul>
    <a href="{{ route('listes.membres', $liste->id) }}">Voir les membres</a>
@endsection
