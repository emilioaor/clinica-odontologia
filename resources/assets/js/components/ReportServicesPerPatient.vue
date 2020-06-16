<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de último servicio por paciente
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
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for=""> Solo archivos muertos </label>
                                        <br>
                                        <input 
                                            type="checkbox"
                                            v-model="data.deadFile"
                                            id="deadFile"
                                            name="deadFile"
                                        >
                                            
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

                            <div class="row" v-if="data.services.length">

                                <div class="col-xs-12">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th width="12%">Fecha</th>
                                                <th width="20%">Paciente</th>
                                                <th width="20%">Archivo muerto</th>
                                                <!--
                                                <th>Tipo</th>
                                                <th>Diente</th>
                                                <th>Doctor</th>
                                                <th>Asistente</th>
                                                <th>Precio</th>
                                                <th>Qty</th>
                                                <th>Total</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="service in data.services">
                                                <tr>
                                                    <td>{{ dateFormat(service.created_at) }}</td>
                                                    <td>{{ service.patient.name }}</td>
                                                    <td>
                                                       <input 
                                                            type="checkbox"
                                                            v-model="service.dead_file"
                                                            @click="changeStatusFile(service.id)"
                                                        >
                                                    </td>
                                                    <!--
                                                    <td>{{ service.product.name }}</td>
                                                    <td>{{ service.tooth }}</td>
                                                    <td>{{ service.doctor.name }}</td>
                                                    <td>{{ service.assistant.name }}</td>
                                                    <td>{{ '$' + service.unit_price }}</td>
                                                    <td>{{ service.qty }}</td>
                                                    <td>{{ '$' + service.price }}</td> -->
                                                </tr>
                                            </template>
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
                patient: null,
                data: {
                    start: '',
                    end: '',
                    patient_id: null,
                    filter: false,
                    deadFile: false,
                    services: []
                },
                modal: {
                    data: [],
                    loading: false,
                    search: ''
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
                let url =  '/admin/report/servicesPerPatientData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&dead=' + this.data.deadFile

                if('{{ Auth::user()->last_service }}') {
                    url = '/user/report/servicesPerPatientDataUser' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&dead=' + this.data.deadFile
                }
                console.log(url)
                axios.get(url)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.services = Object.values(res.data.services);
                        }
                        this.data.deadFile = false
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.services = [];
                        this.data.deadFile = false
                    })
            },

            changeStatusFile: function (idService) {
                this.loading = true;
                let url =  '/admin/servicesPerPatient/changeStatusFile/' + idService

                if('{{ Auth::user()->last_service }}') {
                    url = '/user/servicesPerPatient/changeStatusFile/' + idService + '/user'
                }
                console.log(url)
                axios.get( url )
                .then((res) => {
                    this.loading = false;
                    this.search()
                })
                .catch((err) => {
                    if (err.response.status === 403 || err.response.status === 405) {
                        location.href = '/';
                    }
                    console.log(err);
                })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
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

                this.data.services.forEach((service) => {
                    service.payments.forEach((payment) => {
                        total += payment.amount;
                    });
                });

                return total;
            },

            getBalance: function () {
                return this.getTotalPayments() - this.getTotalServices();
            }
        }
    }
</script>