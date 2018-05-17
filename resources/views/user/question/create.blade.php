@extends('layouts.app')

@section('content')
    <register-question
        :doctors = "{{ json_encode($doctors) }}"
    ></register-question>
@endsection