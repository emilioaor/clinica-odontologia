@extends('layouts.app')

@section('content')
    <search-expense
        user = "{{ json_encode(Auth::user()) }}"
        :suppliers = "{{ json_encode($suppliers) }}"
    ></search-expense>
@endsection