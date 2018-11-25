@extends('layouts.app')

@section('content')
    <edit-supply-type
        :supply-types = "{{ json_encode($supplyTypes) }}"
    ></edit-supply-type>
@endsection