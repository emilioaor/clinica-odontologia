@extends('layouts.app')

@section('content')
    <report-sell-manager-patients
            :sell-managers = "{{ json_encode($sellManagers) }}"
            :user = "{{ json_encode(Auth::user()) }}"
    ></report-sell-manager-patients>
@endsection