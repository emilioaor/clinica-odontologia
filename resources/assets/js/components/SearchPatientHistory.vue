<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-search" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Busqueda de servicios
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
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
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <p>
                                            {{ patient.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="">Telefono</label>
                                        <p>
                                            {{ patient.phone }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <p>
                                            {{ patient.email }}
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-3">
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

                                <div class="col-sm-3">
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
                                                @click="searchPatientHistory()"
                                                v-if="!loading"
                                                >
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="data.services">
                                <div class="col-xs-12" v-for="services in data.services">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Servicio</th>
                                                <th>Diente</th>
                                                <th>Doctor</th>
                                                <th>Asistente</th>
                                                <th width="10%">Precio</th>
                                                <th width="10%">Recibido</th>
                                                <th width="10%">Balance</th>
                                                <th width="5%" v-if="authUser.level === 1 || authUser.level === 3"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="service in services">
                                                <td>{{ dateFormat(service.created_at) }}</td>
                                                <td>{{ service.product.name }}</td>
                                                <td>{{ service.tooth }}</td>
                                                <td>{{ service.doctor.name }}</td>
                                                <td>{{ service.assistant.name }}</td>
                                                <td>{{ service.price }}</td>
                                                <td>
                                                    <input
                                                            type="number"
                                                            placeholder="Recibido"
                                                            class="form-control"
                                                            min="0"
                                                            v-model="service.amount"
                                                            v-if="service.edit"
                                                            :class="{'input-error': service.amount === '' || service.amount === null}"
                                                        >
                                                    <span v-if="!service.edit">
                                                        {{ service.amount }}
                                                    </span>
                                                    <p
                                                            class="error"
                                                            v-if="service.edit && (service.amount === '' || service.amount === null)"
                                                        >
                                                        Requerido
                                                    </p>
                                                </td>
                                                <td>{{ service.price - service.amount }}</td>
                                                <td v-if="authUser.level === 1 || authUser.level === 3">
                                                    <button
                                                            class="btn btn-warning"
                                                            v-if="!service.edit && ! service.loading"
                                                            @click="service.edit = true"
                                                        >
                                                        <i class="glyphicon glyphicon-pencil"></i>
                                                    </button>


                                                    <button
                                                            class="btn btn-success"
                                                            v-if="service.edit && ! service.loading"
                                                            @click="updateAmount(service)"
                                                        >
                                                        <i class="glyphicon glyphicon-ok"></i>
                                                    </button>

                                                    <img src="/img/loading.gif" v-if="service.loading">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Notes -->
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <hr>
                                            <div
                                                    class="alert alert-info"
                                                    v-for="notes in data.notes"
                                                    v-if="notes[0].date === services[0].formatDate"
                                                >
                                                <p>
                                                    <strong>
                                                        Notas {{ dateFormat(notes[0].date) }}
                                                    </strong>
                                                </p>

                                                <p v-for="(note,id) in notes">
                                                    {{ (id + 1) + '. ' + note.user.username + ' - ' + note.content }}
                                                </p>
                                            </div>
                                            <hr>
                                        </div>
                                    </div>
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
        props: ['user'],
        data: function () {
          return {
              loading: false,
              patient: null,
              initStart: new Date(),
              initEnd: new Date(),
              authUser: JSON.parse(this.user),
              data: {
                  start: '',
                  end: '',
                  services: [],
                  notes: []
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
                        this.modal.loading = false;
                    })
                ;
            },

            selectPatient: function (patient) {
                this.patient = patient;
                this.data.services = [];
                this.data.notes = [];
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

            searchPatientHistory: function () {
                this.loading = true;

                axios.get('/user/service/' + this.patient.public_id + '/search?start=' + this.data.start + '&end=' + this.data.end)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {

                            for (let x in res.data.services) {
                                for (let y in res.data.services[x]) {

                                    res.data.services[x][y].edit = false;
                                    res.data.services[x][y].loading = false;
                                }
                            }

                            this.data.services = res.data.services;
                            this.data.notes = res.data.notes;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                        this.loading = false;
                        this.data.services = [];
                        this.data.notes = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            updateAmount: function (service) {

                if (service.amount === null || service.amount === '') {
                    return false;
                }

                service.loading = true;

                axios.put('/user/service/amount', service)
                    .then((res) => {

                        if (res.data.success) {
                            service.edit = false;
                            service.loading = false;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                        service.loading = false;
                    });
            }
        }
    }
</script>