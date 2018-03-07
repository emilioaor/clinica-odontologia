@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de pacientes
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <table class="table table-responsive table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Telefono</th>
                                    <th>Nombre</th>
                                    <th>Creación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($patients))
                                    @foreach($patients as $patient)
                                        <tr>
                                            <td>
                                                <a href="{{ route('patient.edit', ['patient' => $patient->public_id]) }}">
                                                    #{{ $patient->public_id }}
                                                </a>
                                            </td>
                                            <td>{{ $patient->phone }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ $patient->created_at->format('m/d/Y') }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">
                                            No hay pacientes registrados. <a href="{{ route('patient.create') }}">Registrar paciente</a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12">
                <div class="text-center">
                    {{ $patients->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection