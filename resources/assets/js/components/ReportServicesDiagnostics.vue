<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de servicios diagn&oacute;sticados
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
                                        <label for="start">Desde</label>
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
                                        <label for="end">Hasta</label>
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

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="doctor">Doctor</label>
                                        <select
                                                name="doctor"
                                                id="doctor"
                                                class="form-control"
                                                v-model="data.doctor"
                                                >
                                            <option :value="0">Todos</option>
                                            <option
                                                    v-for="doctor in doctors"
                                                    :value="doctor.id"
                                                    >
                                                {{ doctor.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Total</label>
                                        <p>
                                            $ {{ getAllTotal() }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <button
                                                class="btn btn-primary"
                                                @click="validate()"
                                                v-if="!loading"
                                                >
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-for="servicePerDoctor in data.services">

                                <div class="col-xs-12">
                                    <div class="alert alert-info">
                                        <p>
                                            <strong>Doctor:</strong>
                                            {{ servicePerDoctor[0].diagnostic.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-xs-12">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Paciente</th>
                                                <th>Servicio</th>
                                                <th>Diente</th>
                                                <th>Registrado por</th>
                                                <th>Asistente</th>
                                                <th>Precio</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="service in servicePerDoctor">
                                                <td>{{ dateFormat(service.created_at) }}</td>
                                                <td>{{ service.patient.name }}</td>
                                                <td>{{ service.product.name }}</td>
                                                <td>{{ service.tooth }}</td>
                                                <td>{{ service.doctor.name }}</td>
                                                <td>{{ service.assistant.name }}</td>
                                                <td>{{ service.unit_price }}</td>
                                                <td>{{ service.qty }}</td>
                                                <td>{{ service.price }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>{{ getTotal(servicePerDoctor) }}</th>
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
        props: {
            doctors: {
                type: Array,
                required: true
            }
        },
        data: function () {
            return {
                loading: false,
                initStart: new Date(),
                initEnd: new Date(),
                data: {
                    start: '',
                    end: '',
                    doctor: 0,
                    services: []
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

            validate: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.search();
                    }
                });
            },

            search: function () {
                this.loading = true;

                axios.get(
                        '/admin/report/servicesDiagnosticsData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&doctor=' + this.data.doctor
                )
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.services = res.data.services;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.services = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            getTotal: function (services) {
                let total = 0;

                services.forEach((service) => {
                    total += service.price;
                });

                return total;
            },

            getAllTotal: function () {
                let total = 0;

                for (let i in this.data.services) {
                    total += this.getTotal(this.data.services[i]);
                }

                return total;
            }
        }
    }
</script>