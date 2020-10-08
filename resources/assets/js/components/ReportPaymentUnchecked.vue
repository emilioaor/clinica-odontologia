<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de pacientes con pago sin revisar
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

                            <div class="row">
                                <div class="col-xs-12">

                                    <!-- PaymentUnchecked -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Paciente</th>
                                                <th>Tipo</th>
                                                <th>Servicio</th>
                                                <th class="text-center">Monto</th>
                                                <th class="text-center">Revisado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr :key="payment.id" v-for="payment in data.payments">
                                                <td>{{ dateFormat(payment.date) }}</td>
                                                <td>{{ payment.patient_history.patient.name }}</td>
                                                <td>{{ payment.paymentMethod }}</td>
                                                <td>{{ payment.patient_history.product.name }}</td>
                                                <td class="text-center">${{ payment.amount }}</td>
                                                <td class="text-center">
                                                    <input
                                                            class="payment-checkbox"
                                                            :id="'checked_in_ticket' + payment.id"
                                                            :name="'checked_in_ticket' + payment.id"
                                                            type="checkbox"
                                                            v-model="payment.checked_in_ticket"
                                                            @change="updateChecked(payment)"
                                                            v-if="loadingChecked !== payment.id"
                                                            :disabled="loadingChecked !== false"
                                                    >
                                                    <img src="/img/loading.gif" v-else>
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
                loadingChecked: false,
                initStart: new Date(),
                initEnd: new Date(),
                data: {
                  start: '',
                  end: '',
                  filter: false,
                  payments: []
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
                        '/admin/report/paymentUncheckedData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&filter=' + this.data.filter
                )
                    .then((res) => {
                        this.loading = false;
                        this.data.payments = [];

                        if (res.data.success) {
                            this.data.payments = res.data.payments;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.payments = [];
                    })
            },

            updateChecked: function (payment) {
                this.loadingChecked = payment.id;

                axios.put('/user/payment/' + payment.id + '/checked')
                    .then((res) => {

                        if (res.data.success) {
                            this.loadingChecked = false;
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