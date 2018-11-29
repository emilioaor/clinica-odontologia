@extends('layouts.app')

@section('content')
    <supply-inventory-movement-out
        :supplies = "{{ json_encode($supplies) }}"
        :users = "{{ json_encode($users) }}"
    ></supply-inventory-movement-out>
@endsection