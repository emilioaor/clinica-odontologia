@extends('layouts.app')

@section('content')
    <register-payment
        user = "{{ json_encode(Auth::user()) }}"
    ></register-payment>
@endsection