@extends('layouts.app')

@section('content')
    <edit-supply
        view-data = "{{ json_encode($supply) }}"
    ></edit-supply>
@endsection