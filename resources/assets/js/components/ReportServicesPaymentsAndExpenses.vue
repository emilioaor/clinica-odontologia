<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de servicios, pagos y gastos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">

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
                                    <label for="patient">Paciente</label>
                                    <div class="input-group">
                                        <input
                                                type="text"
                                                class="form-control"
                                                placeholder="Paciente"
                                                id="patient"
                                                name="patient"
                                                v-model="patient.name"
                                                readonly
                                                >
                                        <span class="input-group-btn">
                                            <button
                                                    class="btn btn-danger"
                                                    type="button"
                                                    v-if="patient.id"
                                                    @click="removePatient()"
                                                    >
                                                <i class="glyphicon glyphicon-remove"></i>
                                            </button>

                                            <button
                                                    class="btn btn-primary"
                                                    type="button"
                                                    data-toggle="modal"
                                                    data-target="#patientModal"
                                                    @click="searchPatients()"
                                                    >
                                                <i class="glyphicon glyphicon-search"></i>
                                            </button>
                                        </span>
                                    </div>
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

                        <div class="row" v-for="patientData in data.data">
                            <div class="col-xs-12">

                                <div class="alert alert-info">
                                    <p>
                                        <strong>Paciente:</strong>
                                        {{ patientData.patient.name }}
                                    </p>
                                </div>

                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                            <th>Servicio</th>
                                            <th>Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="report in patientData.data">
                                            <td>{{ dateFormat(report.date) }}</td>
                                            <td>{{ report.type }}</td>
                                            <td>{{ report.service }}</td>
                                            <td>{{ report.amount }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

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
                                    <tr v-for="p in modal.data">
                                        <td>{{ p.phone }}</td>
                                        <td>{{ p.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    data-dismiss="modal"
                                                    @click="selectPatient(p)"
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
                        <button type="button" id="closeModalPatient" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import RegisterExpenseModal from './RegisterExpenseModal';

    export default {
        components: {
            Datepicker,
            RegisterExpenseModal
        },
        props: {

        },

        data: function () {
            return {
                loading: false,
                initStart: new Date(),
                initEnd: new Date(),
                patient: {
                    id: null,
                    name: null
                },
                data: {
                    start: '',
                    end: '',
                    patient: 0,
                    data: []
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
            searchPatients: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/user/patient/budget/search?search=' + this.modal.search)
                        .then((res) => {
                    this.modal.loading = false;

                    this.modal.data = res.data.patients;
                })
                .catch((err) => {
                    if (err.response.status === 403 || err.response.status === 405) {
                        location.href = '/';
                    }
                    this.modal.loading = false;
                })
                ;
            },

            selectPatient: function (patient) {
                this.patient.id = patient.id;
                this.patient.name = patient.name;
                this.data.patient = patient.id;
            },

            removePatient: function () {
                this.patient.id = null;
                this.patient.name = null;
                this.data.patient = 0;
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
                this.data.data = [];

                axios.get(
                        '/admin/report/servicesPaymentsAndExpensesData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&patient=' + this.data.patient
                )
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.data = res.data.data;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },
        }
    }
</script>