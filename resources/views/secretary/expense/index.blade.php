@extends('layouts.app')

@section('content')
    <search-expense
        user = "{{ json_encode(Auth::user()) }}"
    ></search-expense>
@endsection