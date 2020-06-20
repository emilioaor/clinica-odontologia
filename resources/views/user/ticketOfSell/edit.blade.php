@extends('layouts.app')

@section('content')
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file"></i>
                    Ticket de venta
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Nombre</label>
                                    <p>
                                        {{ $ticketOfSell->patient->name }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Telefono</label>
                                    <p>
                                        {{ $ticketOfSell->patient->phone }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <p>
                                        {{ $ticketOfSell->patient->email }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Registrado por</label>
                                    <p>
                                        {{ $ticketOfSell->user->name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <a
                                            class="btn btn-success"
                                            href="{{ route('ticketOfSell.pdf', ['ticketOfSell' => $ticketOfSell->public_id]) }}"
                                            target="_blank"
                                    >
                                        <i class="glyphicon glyphicon-file"></i>
                                        Generar PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xs-12">
                                <table class="table table-responsive">
                                    <tr>
                                        <th>Fecha</th>
                                        @if(Auth::user()->hasRole('admin'))
                                            <th>Código</th>
                                        @endif
                                        <th>Servicio</th>
                                        <th>Diente</th>
                                        <th>Doctor</th>
                                        <th>Asistente</th>
                                    </tr>
                                    @foreach($ticketOfSell->ticketOfSellDetails as $detail)
                                        <tr>
                                            <td>{{ $detail->patientHistory->created_at->format('m/d/Y') }}</td>
                                            @if(Auth::user()->hasRole('admin'))
                                                <td>{{ $detail->patientHistory->public_id }}</td>
                                            @endif
                                            <td>{{ $detail->patientHistory->product->name }}</td>
                                            <td>{{ $detail->patientHistory->tooth }}</td>
                                            <td>{{ $detail->patientHistory->doctor->name }}</td>
                                            <td>{{ $detail->patientHistory->assistant->name }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection