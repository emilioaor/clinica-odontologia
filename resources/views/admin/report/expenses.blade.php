@extends('layouts.app')

@section('content')
    <report-expenses
            :suppliers = "{{ json_encode($suppliers) }}"
    ></report-expenses>
@endsection