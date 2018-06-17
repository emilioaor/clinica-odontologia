@extends('layouts.app')

@section('content')
    <register-patient-reference
        :patient-references = "{{ json_encode($patientReferences) }}"
    ></register-patient-reference>
@endsection