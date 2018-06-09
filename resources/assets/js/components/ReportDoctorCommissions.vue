<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de comisión de doctores
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
                                        data-target="#doctorModal"
                                        @click="searchDoctor()"
                                        >
                                    <i class="glyphicon glyphicon-search"></i>

                                    <span v-if="! doctor">
                                        Seleccionar doctor
                                    </span>
                                    <span v-if="doctor">
                                        Cambiar doctor
                                    </span>
                                </a>
                                <hr>
                            </div>
                        </div>

                        <section v-if="doctor">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <p>
                                            {{ doctor.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Telefono</label>
                                        <p>
                                            {{ doctor.phone }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <p>
                                            {{ doctor.email }}
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="balance">¿Solo balance en 0?</label>
                                        <input
                                                id="balance"
                                                name="balance"
                                                type="checkbox"
                                                v-model="balanceZero"
                                                >
                                    </div>
                                </div>
                            </div>

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
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <button class="btn btn-primary" v-if="!loading" @click="search()">
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-for="p in data.report">
                                <div class="col-xs-12">

                                    <div class="alert alert-info">
                                        <p>
                                            <strong>Paciente:</strong>
                                            {{ p.patient.name }}
                                        </p>
                                    </div>

                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Descripci&oacute;n</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody v-for="d in p.data">
                                            <tr
                                                    v-for="line in d.services"
                                            >
                                                <td>{{ dateFormat(line.date) }}</td>
                                                <td>{{ line.classification }}</td>
                                                <td>{{ line.description }}</td>
                                                <td>{{ line.amount }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h5 class="text-center">
                                                <strong>Servicios:</strong>
                                                {{ totalServices(p.data) }}
                                            </h5>
                                        </div>
                                        <div class="col-sm-3">
                                            <h5 class="text-center">
                                                <strong>Pagos:</strong>
                                                {{ totalPayments(p.data) }}
                                            </h5>
                                        </div>
                                        <div class="col-sm-3">
                                            <h5 class="text-center">
                                                <strong>Balance:</strong>
                                                {{ totalServices(p.data) - totalPayments(p.data) }}
                                            </h5>
                                        </div>

                                        <div class="col-sm-3">
                                            <h5 class="text-center">
                                                <strong>Comisi&oacute;n:</strong>
                                                {{ totalCommission(p.data) }}
                                            </h5>
                                        </div>
                                    </div>

                                    <hr>
                                </div>
                            </div>

                        </section>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>
                                    <strong>
                                        Selecciona al doctor
                                    </strong>
                                </h3>
                            </div>
                            <div class="col-xs-12">
                                <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Buscador"
                                        v-model="modal.search"
                                        @keyup="searchDoctor()"
                                        >
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-xs-12">

                                <table class="table table-responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th width="33%">Telefono</th>
                                        <th width="33%">Nombre</th>
                                        <th width="33%">Email</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="! modal.loading">
                                    <tr v-for="d in modal.data">
                                        <td>{{ d.phone }}</td>
                                        <td>{{ d.name }}</td>
                                        <td>{{ d.email }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectDoctor(d)"
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
              doctor: null,
              initStart: new Date(),
              initEnd: new Date(),
              balanceZero: false,
              data: {
                  start: null,
                  end: null,
                  report: []
              },
              modal: {
                  data: [],
                  loading: false,
                  search: ''
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
        },
        methods: {
            searchDoctor: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/admin/user/search?level=2&search=' + this.modal.search)
                    .then((res) => {
                        this.modal.loading = false;

                        this.modal.data = res.data.users;
                    })
                    .catch((err) => {
                        if (err.response.status === 403) {
                            location.href = '/';
                        }
                        this.modal.loading = false;
                    })
                ;
            },

            selectDoctor: function (doctor) {
                this.doctor = doctor;
            },

            search: function () {
                this.loading = true;

                axios.get(
                        '/admin/report/doctorCommissionsData?doctor=' + this.doctor.public_id +
                        '&start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&balance=' + this.balanceZero
                )
                    .then((res) => {

                        this.loading = false;

                        if (res.data.success) {
                            this.data.report = res.data.response;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                    })
                ;
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

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            totalServices: function (data) {
                let total = 0;
                data =  Object.values(data);

                data.forEach((item) => {
                    item.services.forEach((service) => {
                        if (service.classification === 'Servicio') {
                            total += service.amount;
                        }
                    });
                });

                return total;
            },

            totalPayments: function (data) {
                let total = 0;
                data =  Object.values(data);

                data.forEach((item) => {
                    item.services.forEach((service) => {
                        if (service.classification === 'Pago') {
                            total += service.amount;
                        }
                    });
                });

                return total;
            },

            calculateExpenses: function (services) {
                let total = 0;

                services.forEach((service) => {
                    if (service.classification === 'Gasto') {
                        total += service.amount;
                    }
                });

                return total;
            },

            totalCommission: function (data) {
                let total = 0;
                let expenses;
                let commission;

                data =  Object.values(data);

                data.forEach((item) => {

                    expenses = this.calculateExpenses(item.services);

                    commission = (item.price - expenses) * (item.commission / 100);

                    total += commission;
                });

                return total;
            }

        }
    }
</script>