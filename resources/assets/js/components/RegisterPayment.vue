<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-search" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Busqueda de pagos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-12">
                                <a
                                        data-toggle="modal"
                                        data-target="#patientModal"
                                        @click="searchPatients()"
                                        >
                                    <i class="glyphicon glyphicon-search"></i>

                                    <span v-if="! patient">
                                        Seleccionar paciente
                                    </span>
                                    <span v-if="patient">
                                        Cambiar paciente
                                    </span>
                                </a>
                                <hr>
                            </div>
                        </div>

                        <section v-if="patient">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <p>
                                            {{ patient.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Telefono</label>
                                        <p>
                                            {{ patient.phone }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <p>
                                            {{ patient.email }}
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Servicios</label>
                                        <p>
                                            {{ '$ ' + getTotalServices() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Pagos</label>
                                        <p>
                                            {{ '$ ' + getTotalPayments() }}
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
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="">¿Filtrar?</label>
                                        <input
                                                type="checkbox"
                                                v-model="filter"
                                            >
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-show="filter">
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

                                        <button
                                                class="btn btn-success"
                                                type="button"
                                                data-toggle="modal"
                                                data-target="#paymentModal"
                                                >
                                            <i class="glyphicon glyphicon-usd"></i>
                                            Registrar pago
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="data.services.length || data.payments.length">
                                <div class="col-xs-12">
                                    <h4 class="bg-info text-info">Servicios</h4>
                                </div>

                                <div class="col-xs-12">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Servicio</th>
                                                <th>Diente</th>
                                                <th>Doctor</th>
                                                <th>Asistente</th>
                                                <th>Precio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="service in data.services">
                                                <td>{{ dateFormat(service.created_at) }}</td>
                                                <td>{{ service.product.name }}</td>
                                                <td>{{ service.tooth }}</td>
                                                <td>{{ service.doctor.name }}</td>
                                                <td>{{ service.assistant.name }}</td>
                                                <td>{{ '$' + service.price }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            <div class="row" v-if="data.services.length || data.payments.length">
                                <div class="col-xs-12">
                                    <h4 class="bg-info text-info">Pagos</h4>
                                </div>

                                <div class="col-xs-12">

                                    <!-- Payments -->
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Registrado por</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="payment in data.payments">
                                            <td>{{ dateFormat(payment.created_at) }}</td>
                                            <td>{{ payment.user_created.username }}</td>
                                            <td>
                                                <span v-if="payment.type === 1">Tarjeta de crédito</span>
                                                <span v-if="payment.type === 2">Efectivo</span>
                                                <span v-if="payment.type === 3">Cheque</span>
                                            </td>
                                            <td>{{ '$' + payment.amount }}</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>
                                    <strong>
                                        Selecciona al paciente
                                    </strong>
                                </h3>
                            </div>
                            <div class="col-sm-6">
                                <h3 class="text-right">
                                    <a href="/user/patient/create">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        Registrar paciente
                                    </a>
                                </h3>
                            </div>
                            <div class="col-xs-12">
                                <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Buscador"
                                        v-model="modal.search"
                                        @keyup="searchPatients()"
                                        >
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-xs-12">

                                <table class="table table-responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th width="50%">Telefono</th>
                                        <th width="50%">Nombre</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="! modal.loading">
                                    <tr v-for="p in modal.data" v-if="!patient || patient.id !== p.id">
                                        <td>{{ p.phone }}</td>
                                        <td>{{ p.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectPatient(p)"
                                                    data-dismiss="modal"
                                                    >
                                                <i class="glyphicon glyphicon-ok"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-xs-8">
                                <div class="form-group">
                                    <label for="type">Tipo</label>
                                    <select
                                            name="type"
                                            id="type"
                                            class="form-control"
                                            v-validate
                                            data-vv-rules="required"
                                            data-vv-scope="paymentModal"
                                            :class="{'input-error': errors.has('paymentModal.type')}"
                                            v-model="paymentModal.data.type"
                                        >
                                        <option value="1">Tarjeta de crédito</option>
                                        <option value="2">Efectivo</option>
                                        <option value="3">Cheque</option>
                                    </select>

                                    <p class="error" v-if="errors.firstByRule('type', 'required', 'paymentModal')">
                                        Requerido
                                    </p>
                                </div>
                            </div>

                            <div class="col-xs-4">
                                <div class="form-group">
                                    <label for="amount">Monto</label>
                                    <input
                                            type="number"
                                            id="amount"
                                            name="amount"
                                            class="form-control"
                                            v-validate
                                            data-vv-rules="required|numeric"
                                            data-vv-scope="paymentModal"
                                            :class="{'input-error': errors.has('paymentModal.amount')}"
                                            v-model="paymentModal.data.amount"
                                        >
                                    <p class="error" v-if="errors.firstByRule('amount', 'required', 'paymentModal')">
                                        Requerido
                                    </p>
                                    <p class="error" v-if="errors.firstByRule('amount', 'numeric', 'paymentModal')">
                                        Formato invalido
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" @click="validatePayment()" v-show="! paymentModal.loading">Registrar</button>
                        <button id="closePaymentModal" type="button" class="btn btn-secondary" data-dismiss="modal" v-show="! paymentModal.loading">Cerrar</button>
                        <img src="/img/loading.gif" v-if="paymentModal.loading">
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
              patient: null,
              initStart: new Date(),
              initEnd: new Date(),
              filter: false,
              data: {
                  start: '',
                  end: '',
                  services: [],
                  payments: []
              },
              modal: {
                  data: [],
                  loading: false,
                  search: ''
              },
              paymentModal: {
                  data: {
                      amount: null,
                      type: null,
                      patient_id: null
                  },
                  loading: false,
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
            searchPatients: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/user/patient/budget/search?search=' + this.modal.search)
                    .then((res) => {
                        this.modal.loading = false;

                        this.modal.data = res.data.patients;
                    })
                    .catch((err) => {
                        this.modal.loading = false;
                    })
                ;
            },

            selectPatient: function (patient) {
                this.patient = patient;
                this.paymentModal.data.patient_id = patient.id;
                this.data.services = [];
                this.data.payments = [];
                this.search();
            },

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

                axios.get('/user/payment/' + this.patient.public_id + '/search?start=' + this.data.start + '&end=' + this.data.end + '&all=' + !this.filter)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.services = res.data.services;
                            this.data.payments = res.data.payments;
                        }
                    })
                    .catch((err) => {
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

            validatePayment: function () {
                this.$validator.validateAll('paymentModal').then((res) => {

                    if (res) {
                        this.registerPayment();
                    }
                });
            },

            registerPayment: function () {

                this.paymentModal.loading = true;

                axios.post('/user/payment', this.paymentModal.data)
                    .then((res) => {
                        this.paymentModal.loading = false;

                        if (res.data.success) {
                            this.paymentModal.data.type = null;
                            this.paymentModal.data.amount = null;
                            $('#closePaymentModal').click();
                            this.filter = false;
                            this.search();
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                        this.paymentModal.loading = false;
                    })
                ;
            },

            getTotalServices: function () {
                let total = 0;

                for (let i in this.data.services) {
                    total += parseInt(this.data.services[i].price);
                }

                return total;
            },

            getTotalPayments: function () {
                let total = 0;

                for (let i in this.data.payments) {
                    total += parseInt(this.data.payments[i].amount);
                }

                return total;
            },

            getBalance: function () {
                return this.getTotalPayments() - this.getTotalServices();
            }
        }
    }
</script>