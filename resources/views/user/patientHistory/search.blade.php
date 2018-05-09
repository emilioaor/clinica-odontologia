@extends('layouts.app')

@section('content')
    <search-patient-history
        user = "{{ json_encode(Auth::user()) }}"
    ></search-patient-history>
@endsection