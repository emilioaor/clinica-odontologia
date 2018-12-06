@extends('layouts.app')

@section('content')
    <report-inventory-supply
        :supply-brands = "{{ json_encode($supplyBrands) }}"
        :supply-types = "{{ json_encode($supplyTypes) }}"
    ></report-inventory-supply>
@endsection