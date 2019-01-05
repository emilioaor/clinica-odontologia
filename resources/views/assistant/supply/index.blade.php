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

                        <div class="row">
                            <div class="col-sm-3">
                                <select id="supply_brand" class="form-control" onchange="changeBrandOrType()">
                                    <option value="0">- Marca -</option>
                                    @foreach($supplyBrands as $supplyBrand)
                                        <option
                                                value="{{ $supplyBrand->id }}"
                                                {{ Request::has('brand') && Request::get('brand') == $supplyBrand->id ? 'selected' : '' }}
                                        >
                                            {{ $supplyBrand->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-sm-3">
                                <select id="supply_type" class="form-control" onchange="changeBrandOrType()">
                                    <option value="0">- Tipo -</option>
                                    @foreach($supplyTypes as $supplyType)
                                        <option
                                                value="{{ $supplyType->id }}"
                                                {{ Request::has('type') && Request::get('type') == $supplyType->id ? 'selected' : '' }}
                                        >
                                            {{ $supplyType->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <list-supply
                            :supplies = "{{ json_encode($supplies->items()) }}"
                            :supply-brands = "{{ json_encode($supplyBrands) }}"
                            :supply-types = "{{ json_encode($supplyTypes) }}"
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

@section('js')
    <script>
        function changeBrandOrType() {
            const brand = parseInt($('#supply_brand').val());
            const type = parseInt($('#supply_type').val());
            let url = '{{ route('supply.index') }}';
            let separator = '?';

            if (brand > 0) {
                url = url + separator + 'brand=' + brand;
                separator = '&';
            }

            if (type > 0) {
                url = url + separator + 'type=' + type;
            }

            location.href = url;
        }
    </script>
@endsection