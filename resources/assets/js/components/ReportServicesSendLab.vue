<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de servicios enviados a laboratorio
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

                            <div class="row" v-if="data.services.length">

                                <div class="col-xs-12">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Paciente</th>
                                                <th>Servicio</th>
                                                <th>Doctor</th>
                                                <th>Laboratorio</th>
                                                <th>Responsable</th>
                                                <th>Solicitud entrega</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="service in data.services">
                                                <td>{{ dateFormat(service.created_at) }}</td>
                                                <td>{{ service.patient.name }}</td>
                                                <td>{{ service.product.name }}</td>
                                                <td>{{ service.doctor.name }}</td>
                                                <td>{{ service.supplier ? service.supplier.name : '' }}</td>
                                                <td>{{ service.responsible ? service.responsible.name : '' }}</td>
                                                <td>{{ service.delivery_date ? dateTimeFormat(service.delivery_date) : '' }}</td>
                                                <td>{{ service.price }}</td>
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
                        '/admin/report/servicesSendLabData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end
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

            dateTimeFormat: function (dateTime) {
                let time = (dateTime.split(' ')[1]).split(':');
                let date = this.dateFormat(dateTime);

                return date + ' ' + time[0] + ':' + time[1];
            },

            getAllTotal: function () {
                let total = 0;

                for (let i in this.data.services) {
                    total += this.data.services[i].price;
                }

                return total;
            }
        }
    }
</script>