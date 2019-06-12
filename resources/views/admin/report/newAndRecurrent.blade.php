@extends('layouts.app')

@section('content')
    <report-new-and-recurrent
        user = "{{ json_encode(Auth::user()) }}"
    ></report-new-and-recurrent>
@endsection