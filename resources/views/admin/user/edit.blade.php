@extends('layouts.app')

@section('content')
    <edit-user
        :user = "{{ json_encode($user) }}"
        :weekdays = "{{ json_encode($weekdays) }}"
        :translations = "{{ json_encode(trans('message.weekDays')) }}"
    ></edit-user>
@endsection
