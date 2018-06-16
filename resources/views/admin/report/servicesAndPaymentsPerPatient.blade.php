@extends('layouts.app')

@section('content')
    <report-services-and-payments-per-patient
        user = "{{ json_encode(Auth::user()) }}"
    ></report-services-and-payments-per-patient>
@endsection