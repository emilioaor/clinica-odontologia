@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="error-template text-center">
                    <h1>
                        Oops!</h1>
                    <h2>
                        404 Not Found</h2>
                    <div class="error-details">
                        Parece que el contenido que intenta acceder ya no esta disponible!
                    </div>
                    <div class="error-actions">
                        @if(Auth::check())
                            <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                                <i class="glyphicon glyphicon-home"></i>
                                Ir al home
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                <i class="glyphicon glyphicon-home"></i>
                                Ir al login
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection