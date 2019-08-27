@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de productos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form action="{{ route('product.index') }}">
                            <div class="row">
                                <div class="col-sm-5 col-xs-10">
                                    <div class="form-group">
                                        <input
                                                type="text"
                                                name="search"
                                                class="form-control"
                                                value="{{ Request::has('search') ? Request::get('search') : '' }}"
                                                maxlength="30"
                                                placeholder="Busqueda por producto..">
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
                                        <a href="{{ route('product.index') }}" class="text-danger">
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
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($products))
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                                    <a href="{{ route('product.edit', ['product' => $product->public_id]) }}">
                                                        {{ $product->public_id }}
                                                    </a>
                                                @else
                                                    {{ $product->public_id }}
                                                @endif
                                            </td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->priceFormatWithSymbol() }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3">
                                            No hay productos registrados.
                                            @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                                <a href="{{ route('product.create') }}">Registrar producto</a>
                                            @endif
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
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection