@extends('layouts.app')

@section('content')
    <register-budget
        products = "{{ json_encode($products) }}"
    ></register-budget>
@endsection