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

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="payment_type">Tipo de pago</label>
                                        <select
                                                name="payment_type"
                                                id="payment_type"
                                                class="form-control"
                                                v-model="data.payment_type"
                                        >
                                            <option :value="0">- Todos</option>
                                            <option :value="1">Tarjeta de credito</option>
                                            <option :value="2">Efectivo</option>
                                            <option :value="3">Cheque</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Total en comisiones</label>
                                        <p>
                                            $ {{ totalAllCommission() }}
                                        </p>
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

                                        <button
                                                type="button"
                                                class="btn btn-success"
                                                data-toggle="modal"
                                                data-target="#registerExpenseModal"
                                                v-if="!loading && totalAllCommission() > 0"
                                                >
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar gasto por la comisi&oacute;n
                                        </button>

                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-for="(p, pi) in data.report" :key="pi">
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
                                        <tbody v-for="(d, di) in p.data" :key="di">
                                            <tr
                                                    v-for="(line, li) in d.services" 
                                                    :key="li"
                                            >
                                                <td>{{ dateFormat(line.date) }}</td>
                                                <td>
                                                    {{ line.classification }}
                                                    <span v-if="line.paymentType && line.paymentType === 1">
                                                        (Tarjeta de credito)
                                                    </span>
                                                    <span v-else-if="line.paymentType && line.paymentType === 2">
                                                        (Efectivo)
                                                    </span>
                                                    <span v-else-if="line.paymentType && line.paymentType === 3">
                                                        (Cheque)
                                                    </span>
                                                </td>
                                                <td>{{ line.description }}</td>
                                                <td>{{ line.amount }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="row">
                                        <div class="col-sm-2 col-sm-offset-1">
                                            <h5 class="text-center">
                                                <strong>Servicios:</strong>
                                                {{ totalServices(p.data) }}
                                            </h5>
                                        </div>
                                        <div class="col-sm-2">
                                            <h5 class="text-center">
                                                <strong>Pagos:</strong>
                                                {{ totalPayments(p.data) }}
                                            </h5>
                                        </div>
                                        <div class="col-sm-2">
                                            <h5 class="text-center">
                                                <strong>Descuentos:</strong>
                                                {{ totalDiscounts(p.data) }}
                                            </h5>
                                        </div>
                                        <div class="col-sm-2">
                                            <h5 class="text-center">
                                                <strong>Balance:</strong>
                                                {{ totalServices(p.data) - (totalPayments(p.data) + totalDiscounts(p.data)) }}
                                            </h5>
                                        </div>

                                        <div class="col-sm-2">
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

                            <div class="col-xs-12 space-table">

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
                                        <tr
                                                v-for="(d, i) in modal.data"
                                                :key="i"
                                                v-if="! pagination.current || (i >= pagination.current.start && i <= pagination.current.end)"
                                            >
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

                        <div class="row" v-if="pagination.build.length > 0">
                            <div class="col-xs-12 text-center">
                                <!-- Pagination -->
                                <nav aria-label="...">
                                    <ul class="pagination">
                                        <li
                                                v-for="(build, i) in pagination.build"
                                                :key="i"
                                                :class="{active: build.page === pagination.current.page}"
                                                >
                                            <a @click="pagination.current = build">
                                                {{ build.page }}
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="registerExpenseModal" tabindex="-1" role="dialog" aria-labelledby="registerExpenseModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>Registrar un gasto por la comisi&oacute;n</h4>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <input
                                        type="text"
                                        class="form-control"
                                        name="commission"
                                        :class="{'input-error': errors.has('commission')}"
                                        v-model="commission"
                                        v-validate
                                        data-vv-rules="required|regex:^([0-9]+)(\.[0-9]+)?$"
                                        >
                                <p class="error" v-if="errors.firstByRule('commission', 'required')">
                                    Requerido
                                </p>
                                <p class="error" v-if="errors.firstByRule('commission', 'regex')">
                                    Formato invalido
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-show="! loading"
                                >
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                v-show="! loading"
                                @click="validateExpense()"
                                >
                            SI
                        </button>

                        <img src="/img/loading.gif" v-if="loading">
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
                  payment_type: 0,
                  report: []
              },
              modal: {
                  data: [],
                  loading: false,
                  search: ''
              },
              commission: null,

              pagination: {
                  perPage: 999999,
                  build: [],
                  current: null
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
            searchDoctor: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/admin/user/search?level=doctor&limit=9999&search=' + this.modal.search)
                    .then((res) => {
                        this.modal.loading = false;

                        this.modal.data = res.data.users;

                        this.buildPagination();
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
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
                        '&balance=' + this.balanceZero +
                        '&payment_type=' + this.data.payment_type
                )
                    .then((res) => {

                        this.loading = false;

                        if (res.data.success) {
                            this.data.report = res.data.response;
                            this.commission = this.totalAllCommission();
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
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
                    total += this.calculatePayments(item.services);
                });

                return total;
            },

            totalDiscounts: function (data) {
                let total = 0;
                data =  Object.values(data);

                data.forEach((item) => {
                    total += this.calculateDiscount(item.services);
                });

                return total;
            },

            calculateDiscount: function (services) {
                let total = 0;

                services.forEach((service) => {
                    if (service.classification === 'Descuento') {
                        total += service.amount;
                    }
                });

                return total;
            },

            calculatePayments: function (services) {
                let total = 0;

                services.forEach((service) => {
                    if (service.classification === 'Pago') {
                        total += service.amount;
                    }
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
                //let discount;
                let payments;

                data =  Object.values(data);

                data.forEach((item) => {

                    expenses = this.calculateExpenses(item.services);
                    //discount = this.calculateDiscount(item.services);
                    payments = this.calculatePayments(item.services);

                    commission = (payments - expenses) * (item.commission / 100);

                    total += commission;
                });

                return total > 0 ? total : 0;
            },

            totalAllCommission: function () {
                let total = 0;

                Object.values(this.data.report).forEach((patient) => {
                    total += this.totalCommission(patient.data);
                });

                return total;
            },

            validateExpense: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.registerExpense();
                    }
                })
            },

            registerExpense: function () {
                this.loading = true;

                const expense = {
                    amount: this.commission,
                    public_id: this.doctor.public_id
                };

                axios.post('/user/expense/expenseCommission', expense)
                    .then((res) => {

                        if (res.data.success) {
                            location.reload();
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.loading = false;
                        console.log(err);
                    })
            },

            buildPagination: function () {

                this.pagination.build = [];
                this.pagination.current = null;

                if (this.modal.data.length <= this.pagination.perPage) {
                    return;
                }

                let page = 1;
                let start = 0;
                let end = this.pagination.perPage - 1;

                while (start <= this.modal.data.length) {

                    this.pagination.build.push({
                        start: start,
                        end: end,
                        page: page
                    });

                    start += this.pagination.perPage;
                    end = start + (this.pagination.perPage - 1);
                    page++;
                }

                this.pagination.current = this.pagination.build[0];
            }

        }
    }
</script>

<style scoped>
    .pagination a {
        cursor: pointer;
    }
    #doctorModal .space-table {
        max-height: 480px;
        overflow: auto;
    }
</style>