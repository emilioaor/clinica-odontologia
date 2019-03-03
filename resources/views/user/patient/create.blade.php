@extends('layouts.app')

@section('content')
    <register-patient
           :patient-references = "{{ json_encode($patientReferences) }}"
           :user = "{{ json_encode(Auth::user()) }}"
    ></register-patient>
@endsection