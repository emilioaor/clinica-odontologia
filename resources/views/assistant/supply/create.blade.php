@extends('layouts.app')

@section('content')
    <register-supply
        :supply-brands = "{{ json_encode($supplyBrands) }}"
        :supply-types = "{{ json_encode($supplyTypes) }}"
    ></register-supply>
@endsection