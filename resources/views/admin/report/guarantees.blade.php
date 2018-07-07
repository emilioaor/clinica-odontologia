@extends('layouts.app')

@section('content')
    <report-guarantees
        :products = "{{ json_encode($products) }}"
        :doctors = "{{ json_encode($doctors) }}"
    ></report-guarantees>
@endsection