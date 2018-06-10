@extends('layouts.app')

@section('content')
    <edit-appointment
            :products = "{{ json_encode($products) }}"
            :appointment = "{{ json_encode($appointment) }}"
    ></edit-appointment>
@endsection