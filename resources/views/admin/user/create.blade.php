@extends('layouts.app')

@section('content')
    <register-user
            :roles = "{{ json_encode($roles) }}"
    ></register-user>
@endsection