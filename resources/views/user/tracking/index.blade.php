@extends('layouts.app')

@section('content')
    <list-tracking
        :tracking-list = "{{ json_encode($trackingList) }}"
    ></list-tracking>
@endsection