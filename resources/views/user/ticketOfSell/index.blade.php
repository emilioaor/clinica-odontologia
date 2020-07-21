@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de tickets de ventas
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form action="{{ route('ticketOfSell.index') }}">
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
                                        <a href="{{ route('ticketOfSell.index') }}" class="text-danger">
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
                                    <th width="15%">Telefono</th>
                                    <th width="20%">Nombre</th>
                                    <th width="20%">Creación</th>
                                    <th width="20%">Total</th>
                                    <th width="15%">Creado por</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($tickets))
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>
                                                <a href="{{ route('ticketOfSell.edit', ['budget' => $ticket->public_id]) }}">
                                                    {{ $ticket->public_id }}
                                                </a>
                                            </td>
                                            <td>{{ $ticket->patient->phone }}</td>
                                            <td>{{ $ticket->patient->name }}</td>
                                            <td>{{ $ticket->created_at->format('m/d/Y') }}</td>
                                            <td>{{ '$' . number_format($ticket->total, 2) . ' USD' }}</td>
                                            <td>{{ $ticket->user->name ?? '' }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6">
                                            No hay tickets de ventas registrados.
                                            <a href="{{ route('ticketOfSell.create') }}">Registrar ticket de venta</a>
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
                    {{ $tickets->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection