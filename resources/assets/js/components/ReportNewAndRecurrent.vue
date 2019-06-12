<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de pacientes nuevos / recurrentes
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

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Pacientes nuevos</label>
                                        <p>
                                            {{ '$ ' + getTotalAllServicesNew() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Pacientes recurrentes</label>
                                        <p>
                                            {{ '$ ' + getTotalAllServicesRecurrent() }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Cant. Pacientes nuevos</label>
                                        <p>
                                            {{ qtyPatientNew() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Cant. Pacientes recurrentes</label>
                                        <p>
                                            {{ qtyPatientRecurrent() }}
                                        </p>
                                    </div>
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

            getTotalServicesNew: function (services) {
                let total = 0;

                for (let i in services) {
                    if (! services[i].patient.recurrent) {
                        total += parseInt(services[i].price);
                    }
                }

                return total;
            },

            getTotalServicesRecurrent: function (services) {
                let total = 0;

                for (let i in services) {
                    if (services[i].patient.recurrent) {
                        total += parseInt(services[i].price);
                    }
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

            getTotalAllServicesNew: function () {
                let total = 0;

                Object.values(this.data.services).forEach((servicePerPatient) => {
                    total += this.getTotalServicesNew(servicePerPatient);
                });

                return total;
            },

            getTotalAllServicesRecurrent: function () {
                let total = 0;

                Object.values(this.data.services).forEach((servicePerPatient) => {
                    total += this.getTotalServicesRecurrent(servicePerPatient);
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

            qtyPatientNew: function () {
                let total = 0;

                Object.values(this.data.services).forEach((servicePerPatient) => {
                    if (! servicePerPatient[0].patient.recurrent) {
                        total++;
                    }
                });

                return total;
            },

            qtyPatientRecurrent: function () {
                let total = 0;

                Object.values(this.data.services).forEach((servicePerPatient) => {
                    if (servicePerPatient[0].patient.recurrent) {
                        total++;
                    }
                });

                return total;
            },

            getBalance: function () {
                return this.getTotalAllPayments() - this.getTotalAllServices();
            }
        }
    }
</script>