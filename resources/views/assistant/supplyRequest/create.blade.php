@extends('layouts.app')

@section('content')
    <register-supply-request
        supplies-data="{{ json_encode($supplies) }}"
    ></register-supply-request>
@endsection