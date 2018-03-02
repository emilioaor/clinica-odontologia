@extends('layouts.app')

@section('content')
    <edit-budget
        products = "{{ json_encode($products) }}"
        form-data = "{{ json_encode($budget) }}"
        user-logo = "{{ Auth::user()->logo }}"
        user-business-name = "{{ Auth::user()->business_name }}"
    ></edit-budget>
@endsection