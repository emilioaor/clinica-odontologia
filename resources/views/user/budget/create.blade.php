@extends('layouts.app')

@section('content')
    <register-budget
        products = "{{ json_encode($products) }}"
        user-logo = "{{ Auth::user()->logo }}"
        user-business-name = "{{ Auth::user()->business_name }}"
        next = "{{ \App\Budget::nextPublicId() }}"
    ></register-budget>
@endsection