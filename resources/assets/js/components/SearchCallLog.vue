<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-search" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Busqueda de llamadas
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
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
                                        <label for="status">Estatus</label>
                                        <select
                                                name="status"
                                                id="status"
                                                v-model="data.filter"
                                                class="form-control"
                                            >
                                            <option value="0">Todos</option>
                                            <option value="1">Pendiente</option>
                                            <option value="2">Citado</option>
                                            <option value="3">No interesado</option>
                                            <option value="4">No contesto</option>
                                            <option value="5">Volver a llamar</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group input-group">
                                        <input  type="text"
                                                class="form-control"
                                                id="patient"
                                                name="patient"
                                                placeholder="Paciente"
                                                :value="!patient ? '': patient.name"
                                                readonly
                                                >
                                                <span class="input-group-btn">
                                                    <button
                                                            class="btn btn-danger"
                                                            type="button"
                                                            @click="patient = null"
                                                            v-if="patient"
                                                            >
                                                        <i class="glyphicon glyphicon-remove"></i>
                                                    </button>

                                                    <button
                                                            class="btn btn-info"
                                                            type="button"
                                                            data-toggle="modal"
                                                            data-target="#patientModal"
                                                            @click="searchPatients();"
                                                            >
                                                        <i class="glyphicon glyphicon-search"></i>
                                                    </button>
                                                </span>
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

                            <div class="row" v-if="data.calls">
                                <div class="col-xs-12">

                                    <!-- CallLogs -->
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Razón</th>
                                            <th>Paciente</th>
                                            <th>Estatus</th>
                                        </tr>
                                        </thead>
                                        <tbody v-for="call in data.calls">
                                            <tr>
                                                <td>{{ dateFormat(call.call_date)}}</td>
                                                <td>
                                                    <span v-html="call.description"></span>
                                                </td>
                                                <td>{{ call.patient ? call.patient.name : call.call_budget.name }}</td>
                                                <td>{{ data.status[call.status].statusText }}</td>
                                            </tr>

                                            <!-- Status history -->
                                            <tr v-if="call.status_history.length > 1">
                                                <td colspan="4">

                                                    <div class="alert alert-info" v-for="statusHistory in call.status_history">
                                                        <strong>
                                                            {{ data.status[statusHistory.status].statusText }}
                                                            <small>({{ dateFormat(statusHistory.created_at) }})</small>
                                                        </strong><br>
                                                        <span v-html="statusHistory.note"></span>
                                                    </div>

                                                </td>
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
                    filter: 0,
                    calls: [],
                    status: []
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

                let url = '/user/callLog/search/call';

                url += '?start=' + this.data.start;
                url += '&end=' + this.data.end;
                url += '&status=' + this.data.filter;

                if (this.patient !== null) {
                    url += '&patient=' + this.patient.public_id;
                }

                axios.get(url)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.status = res.data.status;
                            this.data.calls = res.data.calls;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.calls = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

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
                this.patient = patient;
                this.data.servicesAndNotes = [];
                this.data.images = [];
            },
        }
    }
</script>