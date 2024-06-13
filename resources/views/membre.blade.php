@extends('layouts.app')

@section('content')
    <h1>Membres de la liste {{ $liste->nom }}</h1>
    <ul>
        @foreach ($membres as $membre)
            <li>{{ $membre->nom }} {{ $membre->prenom }}</li>
        @endforeach
    </ul>
@endsection
