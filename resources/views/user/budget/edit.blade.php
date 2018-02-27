@extends('layouts.app')

@section('content')
    <edit-product
        view-data = "{{ json_encode($product) }}"
    ></edit-product>
@endsection