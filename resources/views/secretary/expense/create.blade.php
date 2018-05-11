@extends('layouts.app')

@section('content')
    <register-expense
        :suppliers = "{{ json_encode($suppliers) }}"
    ></register-expense>
@endsection