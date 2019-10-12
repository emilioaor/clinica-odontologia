<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}?v=1.6.0" rel="stylesheet">
</head>
<body>
    <div id="app">
        @include('layouts.nav')

        @if(Session::has('alert-type') && Session::has('alert-message'))
            <div class="container">
                <div class="alert {{ Session::get('alert-type') }} alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>¡Atención!</strong> {{ Session::get('alert-message') }}
                </div>
            </div>
        @endif

        @yield('content')

        @if(Auth::check())
            <send-lab-notification></send-lab-notification>

            @if(Auth::user()->isSellManager() && Session::has('callBudgets'))
                <call-budget-notification
                    :call-budgets = "{{ json_encode(Session::get('callBudgets')) }}"
                ></call-budget-notification>

                @php
                    Session::forget('callBudgets')
                @endphp
            @endif
        @endif

    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}?v=1.89.0"></script>
    @yield('js')
</body>
</html>
