@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de proveedores
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
                                    <th>Nombre</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(count($suppliers))
                                @foreach($suppliers as $supplier)
                                   <tr>
                                       <td>
                                           <a href="{{ route('supplier.edit', ['supplier' => $supplier->public_id]) }}">
                                               {{ $supplier->public_id }}
                                           </a>
                                       </td>
                                       <td>{{ $supplier->name }}</td>
                                       <td>{{ $supplier->phone }}</td>
                                       <td>{{ $supplier->email }}</td>
                                   </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4">
                                        No hay proveedores registrados.
                                        <a href="{{ route('supplier.create') }}">Registrar proveedor</a>
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
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection