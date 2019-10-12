@extends('layouts.app')

@section('content')
    <list-tracking
        :tracking-list = "{{ json_encode($trackingList) }}"
        :secretaries = "{{ json_encode($secretaries) }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></list-tracking>
@endsection