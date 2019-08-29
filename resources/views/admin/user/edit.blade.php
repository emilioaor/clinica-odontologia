@extends('layouts.app')

@section('content')
    <edit-user
        :user = "{{ json_encode($user) }}"
        :weekdays = "{{ json_encode($weekdays) }}"
        :translations = "{{ json_encode(trans('message.weekDays')) }}"
        :roles = "{{ json_encode($roles) }}"
    ></edit-user>
@endsection
