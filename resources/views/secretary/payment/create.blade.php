@extends('layouts.app')

@section('content')
    <register-payment
        :user = "{{ json_encode(Auth::user()) }}"
        :products = "{{ json_encode($products) }}"
        :doctors = "{{ json_encode($doctors) }}"
        :assistants = "{{ json_encode($assistants) }}"
        :secretaries = "{{ json_encode($secretaries) }}"
    ></register-payment>
@endsection