@extends('layouts.app')

@section('content')
    <register-call
        :users = "{{ json_encode($users) }}"
    ></register-call>
@endsection