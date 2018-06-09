@extends('layouts.app')

@section('content')
    <register-appointment
            :products = "{{ json_encode($products) }}"
    ></register-appointment>
@endsection