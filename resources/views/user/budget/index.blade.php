@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de cotizaciones
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form action="{{ route('budget.index') }}">
                            <div class="row">
                                <div class="col-sm-5 col-xs-10">
                                    <div class="form-group">
                                        <input
                                                type="text"
                                                name="search"
                                                class="form-control"
                                                value="{{ Request::has('search') ? Request::get('search') : '' }}"
                                                maxlength="30"
                                                placeholder="Busqueda por paciente..">
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
                                        <a href="{{ route('budget.index') }}" class="text-danger">
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
                                    <th width="10%">Código</th>
                                    <th width="20%">Telefono</th>
                                    <th width="25%">Nombre</th>
                                    <th width="20%">Creación</th>
                                    <th width="25%">Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($budgets))
                                    @foreach($budgets as $budget)
                                        <tr>
                                            <td>
                                                <a href="{{ route('budget.edit', ['budget' => $budget->public_id]) }}">
                                                    #{{ $budget->public_id }}
                                                </a>
                                            </td>
                                            <td>{{ $budget->patient->phone }}</td>
                                            <td>{{ $budget->patient->name }}</td>
                                            <td>{{ $budget->created_at->format('m/d/Y') }}</td>
                                            <td>{{ '$' . number_format($budget->total_head_value, 2) . ' USD' }}</td>
                                            <td class="text-right">
                                                <a href="{{ route('service.edit', ['service' => $budget->public_id]) }}" class="btn btn-warning">
                                                    <i class="glyphicon glyphicon-pencil"></i>
                                                    Servicios
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4">
                                            No hay cotizaciones registradas. <a href="{{ route('budget.create') }}">Registrar cotización</a>
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
                    {{ $budgets->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection