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

                        <list-supply
                            :supplies = "{{ json_encode($supplies->items()) }}"
                            :supply-brands = "{{ json_encode($supplyBrands) }}"
                            :supply-types = "{{ json_encode($supplyTypes) }}"
                            :last-filter = "{{ json_encode($filters) }}"
                        ></list-supply>
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