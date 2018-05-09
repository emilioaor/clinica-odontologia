@extends('layouts.app')

@section('content')
    <edit-patient
        patient = "{{ json_encode($patient) }}"
        user = "{{ json_encode(Auth::user()) }}"
    ></edit-patient>
@endsection