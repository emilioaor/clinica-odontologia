@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Llamadas pendientes
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
                                    <th>Fecha</th>
                                    <th>Nombre</th>
                                    <th>Telefono</th>
                                    <th>Razón</th>
                                    <th>Estatus</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($callLogs))
                                @foreach($callLogs as $call)
                                    <tr>
                                        <td>{{ $call->callDateTime()->format('m/d/Y') }}</td>
                                        <td>{{ $call->patient->name }}</td>
                                        <td>{{ $call->patient->phone }}</td>
                                        <td>{!! str_replace("\n", '<br>', $call->description) !!}</td>
                                        <td>
                                            {{ $call->statusText() }}
                                        </td>
                                        <td>
                                            <!-- Button trigger modal -->
                                            <button
                                                    type="button"
                                                    class="btn btn-primary"
                                                    data-toggle="modal"
                                                    data-target="#callModal"
                                                    onclick="window.callLogPublicId = '{{ $call->public_id }}'"
                                                >
                                                <i class="glyphicon glyphicon-phone-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6">
                                        No hay llamadas pendientes.
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
                    {{ $callLogs->links() }}
                </div>
            </div>
        </div>

        <change-status-call></change-status-call>

    </div>
@endsection