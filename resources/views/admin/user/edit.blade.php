@extends('layouts.app')

@section('content')
    <edit-user
        user = "{{ json_encode($user) }}"
    ></edit-user>
@endsection
