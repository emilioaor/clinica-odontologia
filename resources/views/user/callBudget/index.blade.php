@extends('layouts.app')

@section('content')
    <sent-budget
        :call-budgets = "{{ json_encode($callBudgets) }}"
    ></sent-budget>
@endsection