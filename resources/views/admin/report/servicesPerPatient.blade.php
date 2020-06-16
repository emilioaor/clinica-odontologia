@extends('layouts.app')

@section('content')
    <report-services-per-patient
        user = "{{ json_encode(Auth::user()) }}"
    ></report-services-per-patient>
@endsection