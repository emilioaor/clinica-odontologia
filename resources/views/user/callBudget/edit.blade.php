@extends('layouts.app')

@section('content')
    <edit-call-budget
        :call-budget-sources = "{{ json_encode($callBudgetSources) }}"
        :call-budget = "{{ json_encode($callBudget) }}"
    ></edit-call-budget>
@endsection