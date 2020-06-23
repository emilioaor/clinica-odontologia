<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Registrar ticket de venta
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
                                                @click="searchPatientHistory()"
                                                v-if="!loading"
                                                >
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar
                                        </button>

                                        <button
                                                class="btn btn-success"
                                                :disabled="serviceSelected.length === 0"
                                                v-if="!loading"
                                                @click="sendForm()"
                                        >
                                            <i class="glyphicon glyphicon-file"></i>
                                            Generar ticket de venta
                                            ({{ serviceSelected.length }})
                                        </button>

                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="data.servicesAndNotes">
                                <div class="col-xs-12">

                                    <template v-for="dataPerDate in data.servicesAndNotes">
                                        <!-- Services -->
                                        <table class="table table-responsive" v-if="dataPerDate.services.length">
                                            <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Servicio</th>
                                                <th class="text-center">Diente</th>
                                                <th>Doctor</th>
                                                <th class="text-center">Precio</th>
                                                <th class="text-center">Pagado</th>
                                                <th class="text-center">Descuento</th>
                                                <th class="text-center">Pendiente por ticket</th>
                                                <th width="5%"></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="service in dataPerDate.services">
                                                <td>{{ dateFormat(service.created_at) }}</td>
                                                <td>{{ service.product.name }}</td>
                                                <td class="text-center">{{ service.tooth }}</td>
                                                <td>{{ service.doctor.name }}</td>
                                                <td class="text-center">${{ service.price }}</td>
                                                <td class="text-center">${{ service.paid }}</td>
                                                <td class="text-center">${{ service.discount }}</td>
                                                <td class="text-center">${{ service.paid_without_ticket }}</td>
                                                <td>
                                                    <button
                                                            class="btn"
                                                            :class="hasServiceSelected(service.id) ? 'btn-success' : 'btn-default'"
                                                            @click="selectService(service.id)"
                                                    >
                                                        <i
                                                                class="glyphicon"
                                                                :class="hasServiceSelected(service.id) ? 'glyphicon-check' : 'glyphicon-certificate'"
                                                        ></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </template>
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
    import RegisterServiceModal from './RegisterPatientHistoryModal.vue';

    export default {
        components: {
            Datepicker
        },
        props: {
            user: {
                required: true
            }
        },
        data: function () {
          return {
              loading: false,
              patient: null,
              initStart: new Date(),
              initEnd: new Date(),
              data: {
                  start: '',
                  end: ''
              },
              serviceSelected: [],
              modal: {
                  data: [],
                  loading: false,
                  search: ''
              },
              authUser: JSON.parse(this.user),
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
            sendForm() {
                if (this.serviceSelected.length) {
                    this.loading = true;

                    axios.post('/user/ticketOfSell', {
                        patient: this.patient.id,
                        services: this.serviceSelected
                    })
                        .then((res) => {
                            if (res.data.success) {
                                location.href = res.data.redirect;
                            }
                        })
                        .catch((err) => {
                            if (err.response.status === 403 || err.response.status === 405) {
                                location.href = '/';
                            }
                            this.loading = false;
                        })
                    ;
                }
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

                axios.get(
                        '/user/service/' + this.patient.public_id + '/search' +
                        '?start=' +this.data.start +
                        '&end=' + this.data.end +
                        '&paymentsWithoutTicket=' + 1
                )
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.servicesAndNotes = res.data.data;
                            this.data.images = res.data.images;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.servicesAndNotes = [];
                        this.data.images = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            selectService(id) {
                if (this.hasServiceSelected(id)) {
                    this.removeServiceSelected(id);
                } else {
                    this.serviceSelected.push(id);
                }
            },

            hasServiceSelected(id) {
                return this.serviceSelected.find(s => s === id)
            },

            removeServiceSelected(id) {
                this.serviceSelected.forEach((s, i) => {
                    if (s === id) {
                        this.serviceSelected.splice(i, 1)
                    }
                })
            }
        }
    }
</script>