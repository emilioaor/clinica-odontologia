<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de pagos
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
                                        <label for="type">Tipo</label>
                                        <select
                                                name="type"
                                                id="type"
                                                class="form-control"
                                                v-model="data.type"
                                                >
                                            <option value="0">Todos</option>
                                            <option value="1">Tarjeta de credito</option>
                                            <option value="2">Efectivo</option>
                                            <option value="3">Cheque</option>
                                            <option value="4">Descuento</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="references">Referencia</label>
                                        <select
                                                name="references"
                                                id="references"
                                                class="form-control"
                                                v-model="data.reference"
                                                >
                                            <option value="0">Todos</option>
                                            <option
                                                    v-for="reference in references"
                                                    :value="reference.id"
                                                    >
                                                {{ reference.description }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <strong>Subtotal:</strong>
                                            ${{ getAllTotal() }}
                                        </p>
                                        <p>
                                            <strong>Descuento:</strong>
                                            ${{ getAllTotalDiscounts() }}
                                        </p>
                                        <p>
                                            <strong>Total:</strong>
                                            ${{ getAllTotalPayments() }}
                                        </p>
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
                                <div class="col-xs-12" v-for="paymentsPerPatient in data.payments">

                                    <!-- Payments -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Paciente</th>
                                                <th>Tipo</th>
                                                <th>Servicio</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="payment in paymentsPerPatient">
                                                <td>{{ dateFormat(payment.created_at) }}</td>
                                                <td>{{ payment.patient_history ? payment.patient_history.patient.name : '' }}</td>
                                                <td>
                                                    <span v-if="payment.type === 1">Tarjeta de credito</span>
                                                    <span v-if="payment.type === 2">Efectivo</span>
                                                    <span v-if="payment.type === 3">Cheque</span>
                                                    <span v-if="payment.type === 4">Descuento</span>
                                                </td>
                                                <td>
                                                    {{ !payment.patient_history ? '' : payment.patient_history.product.name }}
                                                </td>
                                                <td>{{ payment.amount }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="row">
                                        <div class="col-xs-4">
                                            <p class="text-center">
                                                <strong>Subtotal:</strong>
                                                {{ '$' + getTotal(paymentsPerPatient) }}
                                            </p>
                                        </div>

                                        <div class="col-xs-4">
                                            <p class="text-center">
                                                <strong>Descuento:</strong>
                                                {{ '$' + getTotalDiscounts(paymentsPerPatient) }}
                                            </p>
                                        </div>

                                        <div class="col-xs-4">
                                            <p class="text-center">
                                                <strong>Total:</strong>
                                                {{ '$' + getTotalPayments(paymentsPerPatient) }}
                                            </p>
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

    </section>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        components: {
            Datepicker
        },
        props: {
            references: {
                type: Array,
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
                  type: 0,
                  reference: 0,
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
                        '/admin/report/paymentsData?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&type=' + this.data.type +
                        '&reference=' + this.data.reference
                )
                    .then((res) => {
                        this.loading = false;

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

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            getAllTotal: function () {
                let total = 0;

                Object.values(this.data.payments).forEach((paymentPerPatient) => {
                    total += this.getTotal(paymentPerPatient);
                });

                return total;
            },

            getAllTotalDiscounts: function () {
                let total = 0;

                Object.values(this.data.payments).forEach((paymentPerPatient) => {
                    total += this.getTotalDiscounts(paymentPerPatient);
                });

                return total;
            },

            getAllTotalPayments: function () {
                let total = 0;

                Object.values(this.data.payments).forEach((paymentPerPatient) => {
                    total += this.getTotalPayments(paymentPerPatient);
                });

                return total;
            },

            getTotal: function (payments) {
                let total = 0;

                for (let i in payments) {
                    total += parseInt(payments[i].amount);
                }

                return total;
            },

            getTotalPayments: function (payments) {
                let total = 0;

                for (let i in payments) {
                    if (payments[i].type !== 4) { // 4 === Descuento
                        total += parseInt(payments[i].amount);
                    }
                }

                return total;
            },

            getTotalDiscounts: function (payments) {
                let total = 0;

                for (let i in payments) {
                    if (payments[i].type === 4) { // 4 === Descuento
                        total += parseInt(payments[i].amount);
                    }
                }

                return total;
            },
        }
    }
</script>