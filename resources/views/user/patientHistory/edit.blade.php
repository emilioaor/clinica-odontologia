@extends('layouts.app')

@section('content')
    <edit-patient-history
        patient = "{{ json_encode($patient) }}"
        products = "{{ json_encode($products) }}"
        history-date = "{{ $date->format('Y-m-d') }}"
        current-user = "{{ Auth::user() }}"
        assistants = "{{ json_encode($assistants) }}"
        :suppliers = "{{ json_encode($suppliers) }}"
    ></edit-patient-history>
@endsection