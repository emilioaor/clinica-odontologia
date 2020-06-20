@extends('layouts.app')

@section('content')
    <register-ticket-of-sell
            user = "{{ json_encode(Auth::user()) }}"
    ></register-ticket-of-sell>
@endsection