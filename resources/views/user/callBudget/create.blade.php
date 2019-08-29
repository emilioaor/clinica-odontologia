@extends('layouts.app')

@section('content')
    <register-call-budget
        :call-budget-sources = "{{ json_encode($callBudgetSources) }}"
        :sell-managers = "{{ json_encode($sellManagers) }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></register-call-budget>
@endsection