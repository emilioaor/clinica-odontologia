<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de pacientes (Agente de ventas)
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
                                        <label for="sell_manager">Agente de ventas</label>
                                        <select
                                                name="sell_manager"
                                                id="sell_manager"
                                                class="form-control"
                                                v-model="data.sell_manager"
                                                :disabled="user.hasRole.sell_manager"
                                                >
                                            <option value="0">Todos</option>
                                            <option
                                                    v-for="sellManager in sellManagers"
                                                    :value="sellManager.id"
                                                    :key="sellManager.id"
                                                    >
                                                {{ sellManager.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sell_manager">Cita</label>
                                        <select
                                                name="appointment"
                                                id="appointment"
                                                class="form-control"
                                                v-model="data.appointment"
                                        >
                                            <option value="0">Todos</option>
                                            <option value="1">Con cita</option>
                                            <option value="2">Sin cita</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="sell_manager">Servicio</label>
                                        <select
                                                name="service"
                                                id="service"
                                                class="form-control"
                                                v-model="data.service"
                                        >
                                            <option value="0">Todos</option>
                                            <option value="1">Con servicio</option>
                                            <option value="2">Sin servicio</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Pacientes</label>
                                        <p>{{ countPatients() }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Pacientes con cita</label>
                                        <p>{{ countPatientsWithAppointments() }}</p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Pacientes con servicio</label>
                                        <p>{{ countPatientsWithServices() }}</p>
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

                            <div class="row" v-for="sellManager in data.results">
                                <div class="col-xs-12">

                                    <div class="alert alert-info">
                                        <p>
                                            <strong>Agente de ventas:</strong>
                                            {{ sellManager.name }}
                                        </p>
                                    </div>

                                    <!-- Results -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Creación</th>
                                                <th>Tel&eacute;fono</th>
                                                <th>Nombre</th>
                                                <th>Email</th>
                                                <th class="text-center">Cita</th>
                                                <th class="text-center">Servicios</th>
                                                <th class="text-center">Gastado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="patient in sellManager.patients">
                                                <td>{{ dateFormat(patient.created_at) }}</td>
                                                <td>{{ patient.phone }}</td>
                                                <td>{{ patient.name }}</td>
                                                <td>{{ patient.email }}</td>
                                                <td class="text-center">
                                                    <strong>
                                                        <span class="text-success bg-success" v-if="patient.count_appointments > 0">
                                                            SI
                                                        </span>
                                                            <span class="text-danger bg-danger" v-else>
                                                            NO
                                                        </span>
                                                    </strong>
                                                </td>
                                                <td class="text-center">
                                                    <strong>
                                                        <span class="text-success bg-success" v-if="patient.count_services > 0">
                                                            SI
                                                        </span>
                                                            <span class="text-danger bg-danger" v-else>
                                                            NO
                                                        </span>
                                                    </strong>
                                                </td>
                                                <td class="text-center">$ {{ patient.services_amount }}</td>
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
        props: {
            sellManagers: {
                type: Array,
                required: true
            },
            user: {
                type: Object,
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
                    sell_manager: 0,
                    appointment: 0,
                    service: 0,
                    results: []
                },
            }
        },
        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.start = year + '-' + month + '-' + day;
            this.data.end = year + '-' + month + '-' + day;

            if (this.user.hasRole.sell_manager) {
                // Inicializo el agente
                this.data.sell_manager = this.user.id;
            }
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
                        '/admin/report/sellManagerPatientsData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&sell_manager=' + this.data.sell_manager +
                        '&appointment=' + this.data.appointment +
                        '&service=' + this.data.service
                )
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.results = res.data.results;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.results = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            countPatients() {
                let patients = 0;
                for (let i in this.data.results) {
                    patients += this.data.results[i].patients.length;
                }

                return patients;
            },

            countPatientsWithAppointments() {
                let patients = 0;
                for (let i in this.data.results) {
                    this.data.results[i].patients.forEach((patient) => {
                        if (patient.count_appointments > 0) {
                            patients++;
                        }
                    });
                }

                return patients;
            },

            countPatientsWithServices() {
                let patients = 0;
                for (let i in this.data.results) {
                    this.data.results[i].patients.forEach((patient) => {
                        if (patient.count_services > 0) {
                            patients++;
                        }
                    });
                }

                return patients;
            }
        }
    }
</script>