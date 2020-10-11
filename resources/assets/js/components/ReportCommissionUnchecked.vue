<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de comisiones sin pagar
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <section>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <input type="checkbox" v-model="data.filter">
                                        <label for="">¿Filtrar fechas?</label>
                                        <hr>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-show="data.filter">
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

                            <div class="row" v-if="data.doctors.length">
                                <div class="col-xs-12">

                                    <!-- CommissionUnchecked -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Doctor</th>
                                                <th>Paciente</th>
                                                <th>Descripción</th>
                                                <th class="text-center">Monto</th>
                                                <th class="text-center">Pagado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template v-for="doctor in data.doctors">
                                                <tr :key="service.id" v-for="service in doctor.services">
                                                    <td>{{ dateFormat(service.date) }}</td>
                                                    <td>{{ doctor.name }}</td>
                                                    <td>{{ service.patient.name }}</td>
                                                    <td>{{ service.description }}</td>
                                                    <td class="text-center">${{ service.amount }}</td>
                                                    <td class="text-center">
                                                        <div v-if="service.isComplete">
                                                            <input
                                                                    class="payment-checkbox"
                                                                    :id="'mark_as_payed' + service.id"
                                                                    :name="'mark_as_payed' + service.id"
                                                                    type="checkbox"
                                                                    v-model="service.mark_as_payed"
                                                                    @change="updatePayed(service)"
                                                                    v-if="loadingPayed !== service.id"
                                                                    :disabled="loadingPayed !== false"
                                                            >
                                                            <img src="/img/loading.gif" v-else>
                                                        </div>
                                                    </td>
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
                loadingPayed: false,
                initStart: new Date(),
                initEnd: new Date(),
                data: {
                  start: '',
                  end: '',
                  filter: false,
                  doctors: []
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
                        '/admin/report/commissionUncheckedData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&filter=' + this.data.filter
                )
                    .then((res) => {
                        this.loading = false;
                        this.data.doctors = [];

                        if (res.data.success) {
                            this.data.doctors = res.data.doctors;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.doctors = [];
                    })
            },

            updatePayed: function (service) {
                this.loadingPayed = service.id;

                axios.put('/user/service/' + service.id + '/payed')
                    .then((res) => {

                        if (res.data.success) {
                            this.loadingPayed = false;
                        }
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
            }
        }
    }
</script>

<style scoped>
    .payment-checkbox {
        width: 100%;
        height: 20px;
    }
</style>