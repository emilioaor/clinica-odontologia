@extends('layouts.app')

@section('content')
    <report-sotck-supply
        :supply-brands = "{{ json_encode($supplyBrands) }}"
        :supply-types = "{{ json_encode($supplyTypes) }}"
    ></report-sotck-supply>
@endsection