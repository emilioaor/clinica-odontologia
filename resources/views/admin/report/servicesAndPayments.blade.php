@extends('layouts.app')

@section('alerts')
    @include('alerts.paymentsWithoutCheck')
@endsection

@section('content')
    <report-services-and-payments
        :user = "{{ json_encode(Auth::user()) }}"
    ></report-services-and-payments>
@endsection