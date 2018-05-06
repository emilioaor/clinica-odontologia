<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    @if(Auth::guest())
                        <a class="navbar-brand" href="{{ route('login') }}">
                            <i class="glyphicon glyphicon-home"></i>
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    @else
                        <a class="navbar-brand" href="{{ route('home') }}">
                            <i class="glyphicon glyphicon-home"></i>
                            {{ config('app.name', 'Laravel') }}
                        </a>
                    @endif
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    @if(Auth::check())
                        <ul class="nav navbar-nav">
                            @if(! Auth::user()->isAssistant())
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Productos <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                            <li>
                                                <a href="{{ route('product.create') }}">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Agregar producto
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('product.index') }}">
                                                <i class="glyphicon glyphicon-list-alt"></i>
                                                Lista de productos
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Pacientes <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                            <li>
                                                <a href="{{ route('patient.create') }}">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Agregar paciente
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('patient.index') }}">
                                                <i class="glyphicon glyphicon-list-alt"></i>
                                                Lista de pacientes
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Cotizaciones <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                            <li>
                                                <a href="{{ route('budget.create') }}">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Generar cotización
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('budget.index') }}">
                                                <i class="glyphicon glyphicon-list-alt"></i>
                                                Lista de cotizaciones
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                @if(Auth::user()->isAdmin())
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                            Usuarios <span class="caret"></span>
                                        </a>

                                        <ul class="dropdown-menu" role="menu">
                                            <li>
                                                <a href="{{ route('user.create') }}">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Agregar usuario
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('user.index') }}">
                                                    <i class="glyphicon glyphicon-list-alt"></i>
                                                    Lista de usuarios
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            @endif
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Servicios <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                        <li>
                                            <a href="{{ route('service.create') }}">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                Registrar servicio
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('service.search') }}">
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar servicios
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @if(Auth::user()->isAdmin() || Auth::user()->isSecretary())
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Llamadas <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        @if(Auth::user()->isAdmin() || Auth::user()->isDoctor())
                                            <li>
                                                <a href="{{ route('callLog.create') }}">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Registrar llamada
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ route('callLog.index') }}">
                                                <i class="glyphicon glyphicon-list-alt"></i>
                                                Lista de llamadas
                                            </a>
                                        </li>
                                        @if(Auth::user()->isAdmin())
                                            <li>
                                                <a href="{{ route('callLog.search') }}">
                                                    <i class="glyphicon glyphicon-search"></i>
                                                    Buscar llamadas
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Insumos <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    @if(Auth::user()->isAdmin())
                                        <li>
                                            <a href="{{ route('supply.create') }}">
                                                <i class="glyphicon glyphicon-plus"></i>
                                                Registrar insumos
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('supply.index') }}">
                                                <i class="glyphicon glyphicon-list-alt"></i>
                                                Lista de insumos
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('supplyRequest.create') }}">
                                            <i class="glyphicon glyphicon-copy"></i>
                                            Solicitar insumos
                                        </a>
                                    </li>
                                    @if(Auth::user()->isAdmin())
                                        <li>
                                            <a href="{{ route('supplyRequest.index') }}">
                                                <i class="glyphicon glyphicon-list-alt"></i>
                                                Solicitudes de insumos
                                            </a>
                                        </li>
                                            <li>
                                                <a href="{{ route('supplyRequest.search') }}">
                                                    <i class="glyphicon glyphicon-search"></i>
                                                    Reporte de insumos
                                                </a>
                                            </li>
                                    @endif
                                </ul>
                            </li>

                            @if(Auth::user()->isAdmin() || Auth::user()->isSecretary())
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        Pagos <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            <a href="{{ route('payment.create') }}">
                                                <i class="glyphicon glyphicon-search"></i>
                                                Buscar pagos
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    @endif

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::check())
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    <i class="glyphicon glyphicon-user"></i>
                                    {{ Auth::user()->username }}
                                    <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('config') }}">
                                            <i class="glyphicon glyphicon-cog"></i>
                                            Configuración
                                        </a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            <i class="glyphicon glyphicon-log-out"></i>
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </nav>

        @if(Session::has('alert-type') && Session::has('alert-message'))
            <div class="container">
                <div class="alert {{ Session::get('alert-type') }} alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>¡Atención!</strong> {{ Session::get('alert-message') }}
                </div>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('js')
</body>
</html>
