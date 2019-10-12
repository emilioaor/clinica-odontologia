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
                </a>
            @else
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="glyphicon glyphicon-home"></i>
                </a>
            @endif
        </div>

        <div class="collapse navbar-collapse" id="app-navbar-collapse">
            <!-- Left Side Of Navbar -->
            @if(Auth::check())
                <ul class="nav navbar-nav">

                    <!-- Administrador -->
                    @if(Auth::user()->hasPermission('product.create') || Auth::user()->hasPermission('product.index') ||
                        Auth::user()->hasPermission('supplier.create') || Auth::user()->hasPermission('supplier.index') ||
                        Auth::user()->hasPermission('user.create') || Auth::user()->hasPermission('user.index') ||
                        Auth::user()->hasPermission('commission.config') || Auth::user()->hasPermission('patientReference.index'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Administrador <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('product.create'))
                                    <li>
                                        <a href="{{ route('product.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Agregar producto
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('product.index'))
                                    <li>
                                        <a href="{{ route('product.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de productos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supplier.create'))
                                    <li>
                                        <a href="{{ route('supplier.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar proveedor
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supplier.index'))
                                    <li>
                                        <a href="{{ route('supplier.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de proveedores
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('user.create'))
                                    <li>
                                        <a href="{{ route('user.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Agregar usuario
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('user.index'))
                                    <li>
                                        <a href="{{ route('user.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de usuarios
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('commission.config'))
                                    <li>
                                        <a href="{{ route('commission.config') }}">
                                            <i class="glyphicon glyphicon-usd"></i>
                                            Comisiones
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('patientReference.index'))
                                    <li>
                                        <a href="{{ route('patientReference.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Referencia de pacientes
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <!-- Patient -->
                    @if(Auth::user()->hasPermission('patient.create') || Auth::user()->hasPermission('patient.index') ||
                        Auth::user()->hasPermission('service.create') || Auth::user()->hasPermission('service.search') ||
                        Auth::user()->hasPermission('budget.create') || Auth::user()->hasPermission('budget.index') ||
                        Auth::user()->hasPermission('expense.create') || Auth::user()->hasPermission('expense.index') ||
                        Auth::user()->hasPermission('payment.create') || Auth::user()->hasPermission('appointment.index') ||
                        Auth::user()->hasPermission('appointment.create'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Pacientes <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('patient.create'))
                                    <li>
                                        <a href="{{ route('patient.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Agregar paciente
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('patient.index'))
                                    <li>
                                        <a href="{{ route('patient.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de pacientes
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('service.create'))
                                    <li>
                                        <a href="{{ route('service.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar servicio
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('service.search'))
                                    <li>
                                        <a href="{{ route('service.search') }}">
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar servicios
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('budget.create'))
                                    <li>
                                        <a href="{{ route('budget.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Generar cotización
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('budget.index'))
                                    <li>
                                        <a href="{{ route('budget.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de cotizaciones
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('payment.create'))
                                    <li>
                                        <a href="{{ route('payment.create') }}">
                                            <i class="glyphicon glyphicon-search"></i>
                                            Pagos e ingresos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('expense.create'))
                                    <li>
                                        <a href="{{ route('expense.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar gastos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('expense.index'))
                                    <li>
                                        <a href="{{ route('expense.index') }}">
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar gastos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('appointment.create'))
                                    <li>
                                        <a href="{{ route('appointment.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar cita
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('appointment.index'))
                                    <li>
                                        <a href="{{ route('appointment.index') }}">
                                            <i class="glyphicon glyphicon-calendar"></i>
                                            Calendario de citas
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <!-- CallLog -->
                    @if(Auth::user()->hasPermission('callLog.create') || Auth::user()->hasPermission('callLog.index') ||
                        Auth::user()->hasPermission('callLog.search'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Llamadas <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('callLog.create'))
                                    <li>
                                        <a href="{{ route('callLog.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar llamada
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('callLog.index'))
                                    <li>
                                        <a href="{{ route('callLog.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de llamadas
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('callLog.search'))
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

                    <!-- Supply -->
                    @if(Auth::user()->hasPermission('supply.create') || Auth::user()->hasPermission('supply.index') ||
                        Auth::user()->hasPermission('supplyRequest.create') || Auth::user()->hasPermission('supplyRequest.index') ||
                        Auth::user()->hasPermission('supplyRequest.search') || Auth::user()->hasPermission('supply.inventoryType') ||
                        Auth::user()->hasPermission('supply.inventoryBrand') || Auth::user()->hasPermission('supply.inventoryIn') ||
                        Auth::user()->hasPermission('supply.inventoryOut'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Insumos <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('supply.create'))
                                    <li>
                                        <a href="{{ route('supply.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supply.index'))
                                    <li>
                                        <a href="{{ route('supply.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supplyRequest.create'))
                                    <li>
                                        <a href="{{ route('supplyRequest.create') }}">
                                            <i class="glyphicon glyphicon-copy"></i>
                                            Solicitar insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supplyRequest.index'))
                                    <li>
                                        <a href="{{ route('supplyRequest.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Solicitudes de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supplyRequest.search'))
                                    <li>
                                        <a href="{{ route('supplyRequest.search') }}">
                                            <i class="glyphicon glyphicon-search"></i>
                                            Reporte de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supply.inventoryType'))
                                    <li>
                                        <a href="{{ route('supplyType.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Tipos de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supply.inventoryBrand'))
                                    <li>
                                        <a href="{{ route('supplyBrand.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Marcas de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supply.inventoryIn'))
                                    <li>
                                        <a href="{{ route('supplyInventoryMovement.createIn') }}">
                                            <i class="glyphicon glyphicon-log-in"></i>
                                            Entrada de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('supply.inventoryOut'))
                                    <li>
                                        <a href="{{ route('supplyInventoryMovement.createOut') }}">
                                            <i class="glyphicon glyphicon-log-out"></i>
                                            Salida de insumos
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <!-- Question -->
                    @if(Auth::user()->hasPermission('question.create') || Auth::user()->hasPermission('question.index'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Preguntas <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('question.create'))
                                    <li>
                                        <a href="{{ route('question.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar pregunta
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('question.index'))
                                    <li>
                                        <a href="{{ route('question.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de preguntas
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <!-- Reportes -->
                    @if(Auth::user()->hasPermission('report.servicesAndPayments') || Auth::user()->hasPermission('report.doctorCommissions') ||
                        Auth::user()->hasPermission('report.expenses') || Auth::user()->hasPermission('report.payments') ||
                        Auth::user()->hasPermission('report.servicesAndPaymentsPerPatient') || Auth::user()->hasPermission('report.guarantees') ||
                        Auth::user()->hasPermission('report.patientsAndPatientsWithServices') || Auth::user()->hasPermission('report.budgets') ||
                        Auth::user()->hasPermission('report.servicesPaymentsAndExpenses') || Auth::user()->hasPermission('report.servicesDiagnostics') ||
                        Auth::user()->hasPermission('report.servicesSendLab') || Auth::user()->hasPermission('report.inventorySupply') ||
                        Auth::user()->hasPermission('report.inventorySupplyMovement') || Auth::user()->hasPermission('report.sellManagerPatients') ||
                        Auth::user()->hasPermission('report.callLog'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Reportes <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('report.servicesAndPayments'))
                                    <li>
                                        <a href="{{ route('report.servicesAndPayments') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Servicios y pagos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.servicesAndPaymentsParPatient'))
                                    <li>
                                        <a href="{{ route('report.servicesAndPaymentsPerPatient') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Servicios y pagos por paciente
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.doctorCommissions'))
                                    <li>
                                        <a href="{{ route('report.doctorCommissions') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Comisión de doctores
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.expenses'))
                                    <li>
                                        <a href="{{ route('report.expenses') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Gastos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.payments'))
                                    <li>
                                        <a href="{{ route('report.payments') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Pagos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.guarantees'))
                                    <li>
                                        <a href="{{ route('report.guarantees') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Garant&iacute;as
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.patientsAndPatientsWithServices'))
                                    <li>
                                        <a href="{{ route('report.patientsAndPatientsWithServices') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Pacientes / Pacientes con servicios
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.budgets'))
                                    <li>
                                        <a href="{{ route('report.budgets') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Cotizaciones
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.servicesPaymentsAndExpenses'))
                                    <li>
                                        <a href="{{ route('report.servicesPaymentsAndExpenses') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Servicios, pagos y gastos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.servicesDiagnostics'))
                                    <li>
                                        <a href="{{ route('report.servicesDiagnostics') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Servicios diagn&oacute;sticados
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.servicesSendLab'))
                                    <li>
                                        <a href="{{ route('report.servicesSendLab') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Servicios enviados a laboratorio
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.inventorySupply'))
                                    <li>
                                        <a href="{{ route('report.inventorySupply') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Inventario de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.inventorySupplyMovement'))
                                    <li>
                                        <a href="{{ route('report.inventorySupplyMovement') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Movimientos de insumos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.sellManagerPatients'))
                                    <li>
                                        <a href="{{ route('report.sellManagerPatients') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Pacientes (Agente de ventas)
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.newAndRecurrent'))
                                    <li>
                                        <a href="{{ route('report.newAndRecurrent') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Pacientes nuevos / recurrentes
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('report.callLog'))
                                    <li>
                                        <a href="{{ route('report.callLog') }}">
                                            <i class="glyphicon glyphicon-file"></i>
                                            Llamadas
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <!-- Post venta -->
                    @if(Auth::user()->hasPermission('email.index') || Auth::user()->hasPermission('tracking.create') ||
                        Auth::user()->hasPermission('tracking.index'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Post venta <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('email.index'))
                                    <li>
                                        <a href="{{ route('email.index') }}">
                                            <i class="glyphicon glyphicon-envelope"></i>
                                            Correos
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('tracking.create'))
                                    <li>
                                        <a href="{{ route('tracking.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar seguimiento
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('tracking.index'))
                                    <li>
                                        <a href="{{ route('tracking.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Lista de seguimientos
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    <!-- venta -->
                    @if(Auth::user()->hasPermission('callBudgetSource.index') || Auth::user()->hasPermission('callBudget.index') ||
                        Auth::user()->hasPermission('callBudget.create'))

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                Presupuesto <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if(Auth::user()->hasPermission('callBudget.create'))
                                    <li>
                                        <a href="{{ route('callBudget.create') }}">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar envío de presupuesto
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('callBudget.index'))
                                    <li>
                                        <a href="{{ route('callBudget.index') }}">
                                            <i class="glyphicon glyphicon-send"></i>
                                            Presupuestos enviados
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('callBudget.index'))
                                    <li>
                                        <a href="{{ route('callBudget.indexTable') }}">
                                            <i class="glyphicon glyphicon-send"></i>
                                            Presupuestos enviados (tabla)
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('callBudgetSource.index'))
                                    <li>
                                        <a href="{{ route('callBudgetSource.index') }}">
                                            <i class="glyphicon glyphicon-list-alt"></i>
                                            Fuentes de presupuesto
                                        </a>
                                    </li>
                                @endif
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