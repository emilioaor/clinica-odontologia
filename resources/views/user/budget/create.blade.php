@extends('layouts.app')

@section('content')
    <register-budget
        products = "{{ json_encode($products) }}"
        user = "{{ Auth::user() }}"
        next = "{{ \App\Budget::nextPublicId() }}"
        :users = "{{ json_encode($users) }}"
    ></register-budget>
@endsection