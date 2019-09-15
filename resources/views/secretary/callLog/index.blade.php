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
                                    <th>Telefono / Email</th>
                                    <th>Razón</th>
                                    <th>Estatus</th>
                                    <th width="5%"></th>
                                    @if(Auth::user()->isAdmin())
                                        <th width="5%"></th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($callLogs))
                                @foreach($callLogs as $call)
                                    <tr>
                                        <td>{{ $call->callDateTime()->format('m/d/Y') }}</td>
                                        <td>{{ $call->patient ? $call->patient->name : $call->callBudget->name }}</td>
                                        <td>
                                            {{ $call->patient ? $call->patient->phone :
                                                (
                                                    $call->callBudget->isContactTypeEmail() ?
                                                        $call->callBudget->email :
                                                        $call->callBudget->phone
                                                )
                                            }}
                                        </td>
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
                                                    onclick="eventCallLogModal(
                                                        '{{ $call->public_id }}',
                                                        {{ $call->patient ? $call->patient->cancel_appointment : false }}
                                                    )"
                                                >
                                                <i class="glyphicon glyphicon-phone-alt"></i>
                                            </button>
                                        </td>
                                        @if(Auth::user()->isAdmin())
                                            <td>
                                                <!-- Button trigger modal -->
                                                <button
                                                        type="button"
                                                        class="btn btn-danger"
                                                        data-toggle="modal"
                                                        data-target="#deleteModal{{ $call->id }}"
                                                        >
                                                    <i class="glyphicon glyphicon-remove"></i>
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade" id="deleteModal{{ $call->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $call->id }}" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-body">
                                                                <h4>¿Esta seguro de eliminar esta llamada?</h4>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <form method="post" action="{{ route('callLog.destroy', ['callLog' => $call->public_id]) }}">
                                                                    {{ csrf_field() }}
                                                                    {{ method_field('DELETE') }}

                                                                    <button
                                                                            type="button"
                                                                            class="btn btn-secondary"
                                                                            data-dismiss="modal">
                                                                        NO
                                                                    </button>
                                                                    <button
                                                                            class="btn btn-danger">
                                                                        SI
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>

                                    @if(count($call->statusHistory) > 1)
                                        <tr>
                                            <td colspan="{{ Auth::user()->isAdmin() ? '7' : '6' }}">
                                                @foreach($call->statusHistory as $statusHistory)
                                                    <div class="alert alert-info">
                                                        <p>
                                                            <strong>
                                                                {{ $statusHistory->statusText() }}
                                                                <small>({{ $statusHistory->created_at->format('m/d/Y')  }})</small>
                                                            </strong><br>
                                                            {!! str_replace("\n", '<br>', $statusHistory->note) !!}
                                                        </p>
                                                    </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endif

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

@section('js')
    <script>
        function eventCallLogModal(publicId, cancelAppointment)
        {
            window.callLogPublicId = publicId;
            window.cancelAppointment = cancelAppointment;
        }
    </script>
@endsection