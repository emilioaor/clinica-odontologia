@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Solicitudes de insumos
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
                                    <th>Fecha</th>
                                    <th>Usuario</th>
                                    <th>Estatus</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($supplyRequests))
                                    @foreach($supplyRequests as $supplyRequest)
                                        <tr>
                                            <td>{{ $supplyRequest->public_id }}</td>
                                            <td>{{ $supplyRequest->created_at->format('m/d/Y') }}</td>
                                            <td>{{ $supplyRequest->user->name }}</td>
                                            <td>{{ $supplyRequest->statusText() }}</td>
                                            <td>
                                                <button
                                                        class="btn btn-primary"
                                                        data-toggle="modal"
                                                        data-target="#supplyRequestModal{{ $supplyRequest->public_id }}"
                                                    >
                                                    <i class="glyphicon glyphicon-ok-circle"></i>
                                                </button>

                                                <change-status-supply-request
                                                    public-id="{{ $supplyRequest->public_id }}"
                                                    details="{{ json_encode($supplyRequest->supplyRequestDetails) }}"
                                                ></change-status-supply-request>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">
                                            No hay solicitudes de insumo pendientes.
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
                    {{ $supplyRequests->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection