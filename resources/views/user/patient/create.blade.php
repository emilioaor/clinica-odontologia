@extends('layouts.app')

@section('content')
    <register-patient
           :patient-references = "{{ json_encode($patientReferences) }}"
    ></register-patient>
@endsection