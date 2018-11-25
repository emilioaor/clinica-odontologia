@extends('layouts.app')

@section('content')
    <edit-supply
        :view-data = "{{ json_encode($supply) }}"
        :user = "{{ json_encode(Auth::user()) }}"
        :supply-brands = "{{ json_encode($supplyBrands) }}"
        :supply-types = "{{ json_encode($supplyTypes) }}"
    ></edit-supply>
@endsection