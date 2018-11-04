@extends('layouts.app')

@section('content')
    <upload-image
        :patient = "{{ $patient }}"
    ></upload-image>
@endsection