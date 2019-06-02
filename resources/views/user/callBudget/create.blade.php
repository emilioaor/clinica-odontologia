@extends('layouts.app')

@section('content')
    <register-call-budget
        :call-budget-sources = "{{ json_encode($callBudgetSources) }}"
    ></register-call-budget>
@endsection