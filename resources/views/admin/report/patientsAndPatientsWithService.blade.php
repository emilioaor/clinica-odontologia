@extends('layouts.app')

@section('content')
    <report-patients-with-services
        :references = "{{ json_encode($references) }}"
    ></report-patients-with-services>
@endsection