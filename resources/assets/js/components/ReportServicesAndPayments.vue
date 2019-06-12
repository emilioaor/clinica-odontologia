<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de servicios y pagos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <section>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Servicios</label>
                                        <p>
                                            {{ '$ ' + getTotalAllServices() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Pagos</label>
                                        <p>
                                            {{ '$ ' + getTotalAllPayments() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Balance</label>
                                        <p>
                                            {{ '$ ' + getBalance() }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="filter">¿Solo servicios sin pagos asociados?</label>
                                        <input
                                                type="checkbox"
                                                v-model="data.filter"
                                                id="filter"
                                                name="filter"
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Desde</label>
                                        <datepicker
                                                name = "start"
                                                id = "start"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changeStart($event)"
                                                v-model="initStart"
                                        ></datepicker>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Hasta</label>
                                        <datepicker
                                                name = "end"
                                                id = "end"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changeEnd($event)"
                                                v-model="initEnd"
                                        ></datepicker>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <button
                                                class="btn btn-primary"
                                                @click="search()"
                                                v-if="!loading"
                                        >
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="Object.keys(data.services).length">
                                <div class="col-xs-12">
                                    <h4 class="bg-info text-info">Servicios</h4>
                                </div>

                                <div class="col-xs-12" v-for="servicesPerPatient in data.services">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th width="12%">Fecha</th>
                                            <th width="20%">Paciente</th>
                                            <th>Servicio</th>
                                            <th>Diente</th>
                                            <th>Doctor</th>
                                            <th>Asistente</th>
                                            <th>Precio</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="service in servicesPerPatient">
                                            <td>{{ dateFormat(service.created_at) }}</td>
                                            <td>{{ service.patient.name }}</td>
                                            <td>{{ service.product.name }}</td>
                                            <td>{{ service.tooth }}</td>
                                            <td>{{ service.doctor.name }}</td>
                                            <td>{{ service.assistant.name }}</td>
                                            <td>{{ '$' + service.unit_price }}</td>
                                            <td>{{ service.qty }}</td>
                                            <td>{{ '$' + service.price }}</td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ '$' + getTotalServices(servicesPerPatient) }}</td>
                                        </tr>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>

                            <div class="row" v-if="Object.keys(data.payments).length">

                                <div class="col-xs-12">
                                    <h4 class="bg-info text-info">Pagos</h4>
                                </div>

                                <div class="col-xs-12" v-for="paymentsPerPatient in data.payments">

                                    <!-- Payments -->
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th width="12%">Fecha</th>
                                            <th width="20%">Paciente</th>
                                            <th>Registrado por</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="payment in paymentsPerPatient">
                                            <td>{{ dateFormat(payment.date) }}</td>
                                            <td>{{ payment.patient_history ? payment.patient_history.patient.name : '' }}</td>
                                            <td>{{ payment.user_created.name }}</td>
                                            <td>
                                                <span v-if="payment.type === 1">Tarjeta de crédito</span>
                                                <span v-if="payment.type === 2">Efectivo</span>
                                                <span v-if="payment.type === 3">Cheque</span>
                                                <span v-if="payment.type === 4">Descuento</span>
                                            </td>
                                            <td>{{ '$' + payment.amount }}</td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ '$' + getTotalPayments(paymentsPerPatient) }}</td>
                                        </tr>
                                        </tfoot>
                                    </table>

                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        components: {
            Datepicker
        },
        data: function () {
            return {
                loading: false,
                initStart: new Date(),
                initEnd: new Date(),
                data: {
                    start: '',
                    end: '',
                    filter: false,
                    services: [],
                    payments: []
                }
            }
        },
        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.start = year + '-' + month + '-' + day;
            this.data.end = year + '-' + month + '-' + day;
        },
        methods: {
            changeStart: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.start = year + '-' + month + '-' + day;
            },

            changeEnd: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.end = year + '-' + month + '-' + day;
            },

            search: function () {
                this.loading = true;

                axios.get(
                    '/admin/report/servicesAndPaymentsData'
                    + '?start=' + this.data.start
                    + '&end=' + this.data.end
                    + '&filter=' + this.data.filter
                )
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.services = res.data.services;
                            this.data.payments = res.data.payments;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.services = [];
                        this.data.payments = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            getTotalServices: function (services) {
                let total = 0;

                for (let i in services) {
                    total += parseInt(services[i].price);
                }

                return total;
            },

            getTotalPayments: function (payments) {
                let total = 0;

                for (let i in payments) {
                    total += parseInt(payments[i].amount);
                }

                return total;
            },

            getTotalAllServices: function () {
                let total = 0;

                Object.values(this.data.services).forEach((servicePerPatient) => {
                    total += this.getTotalServices(servicePerPatient);
                });

                return total;
            },

            getTotalAllPayments: function () {
                let total = 0;

                Object.values(this.data.payments).forEach((paymentPerPatient) => {
                    total += this.getTotalPayments(paymentPerPatient);
                });

                return total;
            },

            getBalance: function () {
                return this.getTotalAllPayments() - this.getTotalAllServices();
            }
        }
    }
</script>