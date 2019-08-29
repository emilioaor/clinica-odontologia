@extends('layouts.app')

@section('content')
    <edit-call-budget
        :call-budget-sources = "{{ json_encode($callBudgetSources) }}"
        :call-budget = "{{ json_encode($callBudget) }}"
        :sell-managers = "{{ json_encode($sellManagers) }}"
        :user = "{{ json_encode(Auth::user()) }}"
    ></edit-call-budget>
@endsection