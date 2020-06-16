@extends('layouts.app')

@section('content')
    <edit-patient
        :patient = "{{ json_encode($patient) }}"
        :photo = "{{ json_encode($photo) }}"
        :user = "{{ json_encode(Auth::user()) }}"
        :patient-references = "{{ json_encode($patientReferences) }}"
    ></edit-patient>
@endsection