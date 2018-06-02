@extends('layouts.app')

@section('content')
    <edit-email
            :products = "{{ json_encode($products) }}"
    ></edit-email>
@endsection