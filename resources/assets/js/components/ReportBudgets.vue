<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de cotizaciones
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

                            <div class="row" v-if="countBudgets(data.budgetsPerDoctor) > 0">

                                <div class="col-xs-12">
                                    <h1 class="bg-success text-success">Resumen por doctor</h1>
                                </div>

                                <div class="col-xs-12">

                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Doctor</th>
                                                <th class="text-center">Cotizaciones</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Doctors -->
                                            <tr v-for="budgetsPerDoctor in data.budgetsPerDoctor">
                                                <td>{{ budgetsPerDoctor[0].user.name }}</td>
                                                <td class="text-center">{{ budgetsPerDoctor.length }}</td>
                                                <td>{{ getTotal(budgetsPerDoctor) }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th class="text-center">{{ countBudgets(data.budgetsPerDoctor) }}</th>
                                                <th>{{ getAllTotal(data.budgetsPerDoctor) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="row"  v-if="countBudgets(data.budgetsPerPatient) > 0">

                                <div class="col-xs-12">
                                    <h1 class="bg-success text-success">Cotizaciones por paciente</h1>
                                </div>

                                <!-- Patients -->
                                <div class="col-xs-12" v-for="budgetsPerPatient in data.budgetsPerPatient">

                                    <div class="alert alert-info">
                                        <p>
                                            <strong>Paciente:</strong>
                                            {{ budgetsPerPatient[0].patient.name }}
                                        </p>
                                    </div>

                                    <!-- Budgets -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>C&oacute;digo</th>
                                                <th>Creado por</th>
                                                <th>Notas</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="budget in budgetsPerPatient">
                                                <td>{{ dateFormat(budget.creation_date_value) }}</td>
                                                <td>{{ budget.public_id }}</td>
                                                <td>{{ budget.user ? budget.user.name : '' }}</td>
                                                <td>{{ budget.notes_value }}</td>
                                                <td>{{ budget.amount }}</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>Total</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>{{ getTotal(budgetsPerPatient) }}</th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <p>
                                        <strong>Cantidad de cotizaciones:</strong>
                                        {{ budgetsPerPatient.length }}
                                    </p>
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
              data: {
                  start: '',
                  end: '',
                  budgetsPerPatient: [],
                  budgetsPerDoctor: []
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
                        '/admin/report/budgetsData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end
                )
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.budgetsPerPatient = res.data.budgets;
                            this.data.budgetsPerDoctor = res.data.budgetsPerDoctor;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.budgetsPerPatient = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            getTotal: function (budgets) {
                let total = 0;

                budgets.forEach(function (budget) {
                    total += budget.amount;
                });

                return total;
            },

            countBudgets: function (budgets) {
                let count = 0;

                for (let i in budgets) {

                    count += budgets[i].length;
                }

                return count;
            },

            getAllTotal: function (budgets) {
                let total = 0;

                for (let i in budgets) {

                    budgets[i].forEach(function (budget) {
                        total += budget.amount;
                    });
                }

                return total;
            }
        }
    }
</script>