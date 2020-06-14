@extends('layouts.app')

@section('content')
    <register-ticket-of-sell
            user = "{{ json_encode(Auth::user()) }}"
            route-pdf = "{{ route('ticketOfSell.pdf') }}"
    ></register-ticket-of-sell>
@endsection