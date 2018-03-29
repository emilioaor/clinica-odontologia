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

                        <form action="{{ route('patient.index') }}">
                            <div class="row">
                                <div class="col-sm-5 col-xs-10">
                                    <div class="form-group">
                                        <input
                                                type="text"
                                                name="search"
                                                class="form-control"
                                                value="{{ Request::has('search') ? Request::get('search') : '' }}"
                                                maxlength="30"
                                                placeholder="Busqueda..">
                                    </div>
                                </div>
                                <div class="col-sm-1 col-xs-2">
                                    <button class="btn btn-default">
                                        <i class="glyphicon glyphicon-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>

                        @if(Request::has('search') && ! empty(Request::get('search')))
                            <div class="row">
                                <div class="col-xs-12">
                                    <p>
                                        <a href="{{ route('patient.index') }}" class="text-danger">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </a>
                                        <strong>Filtrado por:</strong>
                                        {{ Request::get('search') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        <table class="table table-responsive table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Telefono</th>
                                    <th>Nombre</th>
                                    <th>Creación</th>
                                    @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                        <th></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($patients))
                                    @foreach($patients as $patient)
                                        <tr>
                                            <td>
                                                @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                                    <a href="{{ route('patient.edit', ['patient' => $patient->public_id]) }}">
                                                        {{ $patient->public_id }}
                                                    </a>
                                                @else
                                                    {{ $patient->public_id }}
                                                @endif
                                            </td>
                                            <td>{{ $patient->phone }}</td>
                                            <td>{{ $patient->name }}</td>
                                            <td>{{ $patient->created_at->format('m/d/Y') }}</td>
                                            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                                <td class="text-right">
                                                    <a href="{{ route('service.edit', ['service' => $patient->public_id]) }}" class="btn btn-warning">
                                                        <i class="glyphicon glyphicon-pencil"></i>
                                                        Servicios
                                                    </a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">
                                            No hay pacientes registrados.
                                            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                                <a href="{{ route('patient.create') }}">Registrar paciente</a>
                                            @endif
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