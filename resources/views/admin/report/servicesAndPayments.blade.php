@extends('layouts.app')

@section('content')
    <report-services-and-payments
        user = "{{ json_encode(Auth::user()) }}"
    ></report-services-and-payments>
@endsection