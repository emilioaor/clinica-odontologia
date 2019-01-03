@extends('layouts.app')

@section('content')
    <report-expenses
            :suppliers = "{{ json_encode($suppliers) }}"
            :user = "{{ json_encode(Auth::user()) }}"
    ></report-expenses>
@endsection