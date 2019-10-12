@extends('layouts.app')

@section('content')
    <register-tracking
        :secretaries = "{{ json_encode($secretaries) }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></register-tracking>
@endsection