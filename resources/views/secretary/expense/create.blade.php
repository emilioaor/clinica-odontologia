@extends('layouts.app')

@section('content')
    <register-expense
        :suppliers = "{{ json_encode($suppliers) }}"
        :user = "{{ Auth::user() }}"
    ></register-expense>
@endsection