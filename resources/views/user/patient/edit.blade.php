@extends('layouts.app')

@section('content')
    <edit-patient
        patient = "{{ json_encode($patient) }}"
    ></edit-patient>
@endsection