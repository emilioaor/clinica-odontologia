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
                                        <td>{{ $call->created_at->format('m/d/Y') }}</td>
                                        <td>{{ $call->patient->name }}</td>
                                        <td>{{ $call->patient->phone }}</td>
                                        <td></td>
                                        <td>
                                            <form
                                                    action="{{ route('callLog.update', ['call' => $call->public_id]) }}"
                                                    method="post"
                                                    id="statusForm{{ $call->public_id }}"
                                                >
                                                {{ csrf_field() }}
                                                {{ method_field('PUT') }}

                                                <select name="status" class="form-control">
                                                    <option value="{{ \App\CallLog::STATUS_PENDING }}">
                                                        Pendiente
                                                    </option>
                                                    <option value="{{ \App\CallLog::STATUS_SCHEDULED }}">
                                                        Citado
                                                    </option>
                                                    <option value="{{ \App\CallLog::STATUS_NO }}">
                                                        No interesado
                                                    </option>
                                                </select>
                                            </form>
                                        </td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    onclick="$('#statusForm{{ $call->public_id }}').submit();"
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
    </div>
@endsection