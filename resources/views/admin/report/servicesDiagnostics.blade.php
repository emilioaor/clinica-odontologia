@extends('layouts.app')

@section('content')
    <report-services-diagnostics
        :doctors = "{{ $doctors }}"
    ></report-services-diagnostics>
@endsection