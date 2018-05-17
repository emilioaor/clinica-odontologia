@extends('layouts.app')

@section('content')
    <edit-question
        :view-data = "{{ json_encode($question) }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></edit-question>
@endsection