@extends('layouts.app')

@section('content')
    <register-call-budget-source
        :call-budget-sources = "{{ json_encode($callBudgetSources) }}"
    ></register-call-budget-source>
@endsection