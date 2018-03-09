@extends('layouts.app')

@section('content')
    <edit-service
        budget = "{{ json_encode($budget) }}"
        products = "{{ json_encode($products) }}"
    ></edit-service>
@endsection