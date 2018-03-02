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

                        <table class="table table-responsive table-striped">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Cliente</th>
                                    <th>Creación</th>
                                    <th>Total</th>
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
                                            <td>{{ $budget->client_value }}</td>
                                            <td>{{ $budget->created_at->format('m/d/Y') }}</td>
                                            <td>{{ '$' . number_format($budget->total_head_value, 2) . ' USD' }}</td>
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