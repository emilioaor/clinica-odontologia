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
                                                    data-target="#callModal{{ $call->public_id }}"
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

        @foreach($callLogs as $call)
            <!-- Modal -->
            <div class="modal fade" id="callModal{{ $call->public_id }}" tabindex="-1" role="dialog" aria-labelledby="callModal{{ $call->public_id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <form
                        action="{{ route('callLog.update', ['call' => $call->public_id]) }}"
                        method="post"
                    >
                        <div class="modal-content">
                            <div class="modal-body">

                                {{ csrf_field() }}
                                {{ method_field('PUT') }}

                                <div class="form-group">
                                    <label for="status">Estatus</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="{{ \App\CallLog::STATUS_SCHEDULED }}">
                                            Citado
                                        </option>
                                        <option value="{{ \App\CallLog::STATUS_NO }}">
                                            No interesado
                                        </option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="note">Nota</label>
                                    <textarea
                                            name="note"
                                            id="note"
                                            class="form-control"
                                            placeholder="Notas de la llamada"
                                            cols="30"
                                            rows="4"></textarea>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endsection