@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-calendar"></i>
                    Calendario de citas
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-3">
                                <a
                                        class="btn btn-default"
                                        href="{{ route('appointment.index', ['start' => $last->format('Y-m-d')]) }}"
                                        >
                                    <i class="glyphicon glyphicon-chevron-left"></i>
                                </a>

                                <a
                                        class="btn btn-default"
                                        href="{{ route('appointment.index', ['start' => $next->format('Y-m-d')]) }}"
                                        >
                                    <i class="glyphicon glyphicon-chevron-right"></i>
                                </a>

                                <a
                                        class="btn btn-default"
                                        href="{{ route('appointment.index') }}"
                                        >
                                    Hoy
                                </a>
                            </div>

                            <div class="col-xs-6">
                                <h4 class="text-center">
                                    {{ trans('message.' . $start->format('F')) }}
                                    {{ $start->format('Y') }}
                                </h4>
                            </div>

                            <div class="col-xs-3">
                                <a
                                        class="btn btn-success pull-right"
                                        href="{{ route('appointment.create') }}"
                                        >
                                    <i class="glyphicon glyphicon-plus"></i>
                                    Registrar cita
                                </a>
                            </div>
                        </div>

                        <div class="row appointment-calendar">

                            <div class="appointment-calendar__day">
                                <h3>Domingo</h3>
                                <p>{{ $start->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['sunday'] as $appointment)
                                    <p class="appointment-calendar__day-item">
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                            <div class="appointment-calendar__day">
                                <h3>Lunes</h3>
                                <p>{{ $start->modify('+1 day')->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['monday'] as $appointment)
                                    <p class="appointment-calendar__day-item">
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                            <div class="appointment-calendar__day">
                                <h3>Martes</h3>
                                <p>{{ $start->modify('+1 day')->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['tuesday'] as $appointment)
                                    <p class="appointment-calendar__day-item">
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                            <div class="appointment-calendar__day">
                                <h3>Miercoles</h3>
                                <p>{{ $start->modify('+1 day')->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['wednesday'] as $appointment)
                                    <p class="appointment-calendar__day-item">
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                            <div class="appointment-calendar__day">
                                <h3>Jueves</h3>
                                <p>{{ $start->modify('+1 day')->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['thursday'] as $appointment)
                                    <p class="appointment-calendar__day-item">
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                            <div class="appointment-calendar__day">
                                <h3>Viernes</h3>
                                <p>{{ $start->modify('+1 day')->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['friday'] as $appointment)
                                    <p class="appointment-calendar__day-item">
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                            <div class="appointment-calendar__day">
                                <h3>Sabado</h3>
                                <p>{{ $start->modify('+1 day')->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['saturday'] as $appointment)
                                    <p class="appointment-calendar__day-item">
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection