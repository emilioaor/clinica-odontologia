@extends('layouts.app')

@section('content')
    <edit-supplier
        :view-data = "{{ json_encode($supplier) }}"
        :auth-user = "{{ json_encode(Auth::user()) }}"
    ></edit-supplier>
@endsection