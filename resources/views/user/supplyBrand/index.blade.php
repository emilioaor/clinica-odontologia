@extends('layouts.app')

@section('content')
    <edit-supply-brand
        :supply-brands = "{{ json_encode($supplyBrands) }}"
    ></edit-supply-brand>
@endsection