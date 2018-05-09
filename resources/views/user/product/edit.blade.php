@extends('layouts.app')

@section('content')
    <edit-product
        view-data = "{{ json_encode($product) }}"
        user = "{{ json_encode(Auth::user()) }}"
    ></edit-product>
@endsection