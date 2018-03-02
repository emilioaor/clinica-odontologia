@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-cog"></i>
                    Configuración
                </h1>
            </div>
        </div>

        <business-config
                user = "{{ json_encode(Auth::user()) }}"
        ></business-config>

        <change-password></change-password>
    </div>
@endsection