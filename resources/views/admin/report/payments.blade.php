@extends('layouts.app')

@section('content')
    <report-payments
        :references = "{{ json_encode($references) }}"
    ></report-payments>
@endsection