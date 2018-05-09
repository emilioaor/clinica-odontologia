@extends('layouts.app')

@section('content')
    <edit-budget
        products = "{{ json_encode($products) }}"
        form-data = "{{ json_encode($budget) }}"
        user = "{{ json_encode(Auth::user()) }}"
    ></edit-budget>
@endsection

@section('js')
    @if(Session::has('pdf') && Session::get('pdf') == $budget->public_id)

        <script>

            $(window).on("load", function() {
                location.href = '{{ route('budget.pdf.download', ['budget' => $budget->public_id])  }}';
            })

        </script>

    @endif
@endsection