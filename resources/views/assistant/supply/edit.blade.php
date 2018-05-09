@extends('layouts.app')

@section('content')
    <edit-supply
        view-data = "{{ json_encode($supply) }}"
        user = "{{ json_encode(Auth::user()) }}"
    ></edit-supply>
@endsection