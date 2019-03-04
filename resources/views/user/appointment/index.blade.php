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
                                @if(Auth::user()->hasPermission('appointment.create'))
                                    <a
                                            class="btn btn-success pull-right"
                                            href="{{ route('appointment.create') }}"
                                    >
                                        <i class="glyphicon glyphicon-plus"></i>
                                        Registrar cita
                                    </a>
                                @endif
                            </div>
                        </div>

                        <div class="row appointment-calendar">

                            <div class="appointment-calendar__day">
                                <h3>Domingo</h3>
                                <p>{{ $start->format('m/d/Y') }}</p>
                                <hr>

                                @foreach($response['sunday'] as $appointment)
                                    <p
                                            class="appointment-calendar__day-item bg-{{ $appointment->statusClass() }} text-{{ $appointment->statusClass() }}"
                                            data-toggle="modal"
                                            data-target="#appointmentModal{{ $appointment->id }}"
                                        >
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
                                    <p
                                            class="appointment-calendar__day-item bg-{{ $appointment->statusClass() }} text-{{ $appointment->statusClass() }}"
                                            data-toggle="modal"
                                            data-target="#appointmentModal{{ $appointment->id }}"
                                        >
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
                                    <p
                                            class="appointment-calendar__day-item bg-{{ $appointment->statusClass() }} text-{{ $appointment->statusClass() }}"
                                            data-toggle="modal"
                                            data-target="#appointmentModal{{ $appointment->id }}"
                                        >
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
                                    <p
                                            class="appointment-calendar__day-item bg-{{ $appointment->statusClass() }} text-{{ $appointment->statusClass() }}"
                                            data-toggle="modal"
                                            data-target="#appointmentModal{{ $appointment->id }}"
                                        >
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
                                    <p
                                            class="appointment-calendar__day-item bg-{{ $appointment->statusClass() }} text-{{ $appointment->statusClass() }}"
                                            data-toggle="modal"
                                            data-target="#appointmentModal{{ $appointment->id }}"
                                        >
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
                                    <p
                                            class="appointment-calendar__day-item bg-{{ $appointment->statusClass() }} text-{{ $appointment->statusClass() }}"
                                            data-toggle="modal"
                                            data-target="#appointmentModal{{ $appointment->id }}"
                                        >
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
                                    <p
                                            class="appointment-calendar__day-item bg-{{ $appointment->statusClass() }} text-{{ $appointment->statusClass() }}"
                                            data-toggle="modal"
                                            data-target="#appointmentModal{{ $appointment->id }}"
                                        >
                                        <strong>
                                            {{ $appointment->date->format('H:i') }}
                                        </strong>
                                        -
                                        {{ $appointment->patient->name }}
                                    </p>
                                @endforeach
                            </div>

                        </div>

                        <div class="row appointment-calendar__legend">
                            <div class="col-xs-2">
                                <p>
                                    <span class="bg-warning"></span>
                                    Pendiente
                                </p>
                            </div>

                            <div class="col-xs-2">
                                <p>
                                    <span class="bg-info"></span>
                                    No asistio
                                </p>
                            </div>

                            <div class="col-xs-2">
                                <p>
                                    <span class="bg-success"></span>
                                    Completa
                                </p>
                            </div>

                            <div class="col-xs-2">
                                <p>
                                    <span class="bg-danger"></span>
                                    Cancelada
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        @foreach($response as $day)
            @foreach($day as $appointment)
                <!-- Modal -->
                <div class="modal modal fade" id="appointmentModal{{ $appointment->id }}" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel{{ $appointment->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="alert alert-{{ $appointment->statusClass() }}">
                                            <p>
                                                <strong>
                                                    Detalle de la cita
                                                    ({{ $appointment->statusText() }})
                                                </strong>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Paciente</label>
                                            <p>
                                                {{ $appointment->patient->name }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Telefono</label>
                                            <p>
                                                {{ $appointment->patient->phone }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Email</label>
                                            <p>
                                                {{ $appointment->patient->email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Doctor</label>
                                            <p>
                                                {{ $appointment->doctor ? $appointment->doctor->name : '' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Registrada por</label>
                                            <p>
                                                {{ $appointment->user->name }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="">Fecha</label>
                                            <p>
                                                {{ $appointment->date->format('m/d/Y g:i a') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <table class="table table-responsive table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tratamiento</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($appointment->appointmentDetails as $detail)
                                                    <tr>
                                                        <td>{{ $detail->product->name }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">

                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

                                @if($appointment->isPending() || $appointment->isNoAssisted())
                                    <a href="{{ route('appointment.edit', ['id' => $appointment->id]) }}" class="btn btn-warning">
                                        Editar
                                    </a>

                                    <button type="button"
                                            class="btn btn-danger"
                                            data-dismiss="modal"
                                            data-toggle="modal"
                                            data-target="#deleteModal{{ $appointment->id }}">
                                        Cancelar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach

        @foreach($response as $day)
            @foreach($day as $appointment)
                <!-- Modal -->
                <div class="modal fade" id="deleteModal{{ $appointment->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $appointment->id }}" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <h4>¿Esta seguro de cancelar esta cita?</h4>
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('appointment.cancel', ['id' => $appointment->id]) }}" method="post">
                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}

                                    <button
                                            type="button"
                                            class="btn btn-secondary"
                                            data-dismiss="modal"
                                            >
                                        NO
                                    </button>
                                    <button
                                            class="btn btn-danger"
                                            >
                                        SI
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
@endsection