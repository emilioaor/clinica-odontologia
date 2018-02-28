@extends('layouts.app')

@section('content')
    <edit-budget
        products = "{{ json_encode($products) }}"
        form-data = "{{ json_encode($budget) }}"
    ></edit-budget>
@endsection