@extends('layouts.app')

@section('content')
    <register-expense
        :patients = "{{ json_encode($patients) }}"
        :suppliers = "{{ json_encode($suppliers) }}"
    ></register-expense>
@endsection