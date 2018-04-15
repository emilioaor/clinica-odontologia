@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de insumos
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
                                    <th>Insumo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($supplies))
                                    @foreach($supplies as $supply)
                                        <tr>
                                            <td>
                                                <a href="{{ route('supply.edit', ['supply' => $supply->public_id]) }}">
                                                    {{ $supply->public_id }}
                                                </a>
                                            </td>
                                            <td>{{ $supply->name }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2">
                                            No hay insumos registrados.
                                            <a href="{{ route('supply.create') }}">Registrar insumo</a>
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
                    {{ $supplies->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection