@extends('layouts.app')

@section('content')
    <supply-inventory-movement-in
        :supplies = "{{ json_encode($supplies) }}"
        :loans = "{{ json_encode($loans) }}"
    ></supply-inventory-movement-in>
@endsection