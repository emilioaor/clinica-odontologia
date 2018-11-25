@extends('layouts.app')

@section('content')
    <supply-inventory-movement-in
        :supplies = "{{ json_encode($supplies) }}"
    ></supply-inventory-movement-in>
@endsection