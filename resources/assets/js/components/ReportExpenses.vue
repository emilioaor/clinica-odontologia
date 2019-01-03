<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de gastos
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
                                        <label for="type">Tipo de gasto</label>
                                        <select
                                                name="type"
                                                id="type"
                                                class="form-control"
                                                v-model="data.type"
                                                v-validate
                                                data-vv-rules="required"
                                                :class="{'input-error': errors.has('type')}"
                                                >
                                            <option value="0">Todos</option>
                                            <option value="1">Oficina</option>
                                            <option value="2">Servicios generales</option>
                                            <option value="3">RRHH</option>
                                            <option value="4">Mantenimiento</option>
                                            <option value="5">Farmacia</option>
                                            <option value="6">Implantes</option>
                                            <option value="7">Deposito dental</option>
                                            <option value="8">Laboratorio</option>
                                            <option value="9">Imagenes</option>
                                            <option value="10">Comision de doctores</option>
                                        </select>
                                        <p class="error" v-if="errors.firstByRule('type', 'required')">
                                            Campo requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Total</label>
                                        <p>{{ getTotal() }} $</p>
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

                                        <button
                                                type="button"
                                                class="btn btn-success"
                                                data-toggle="modal"
                                                data-target="#registerExpenseModal"
                                                >
                                            Registrar gasto
                                        </button>

                                        <register-expense-modal
                                            modal-id = "registerExpenseModal"
                                            @register="search()"
                                            :user="user"
                                        ></register-expense-modal>

                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">

                                    <!-- Expenses -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Proveedor</th>
                                                <th>Descripci&oacute;n</th>
                                                <th>Paciente</th>
                                                <th>Servicio</th>
                                                <th>Doctor</th>
                                                <th>Monto</th>
                                                <th width="5%"></th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(e, i) in data.expenses" :key="i">
                                                <td>
                                                    <!-- Date -->
                                                    <div v-if="editExpense === i">
                                                        <datepicker
                                                                :name = "'date' + i"
                                                                :id = "'date' + i"
                                                                language="es"
                                                                input-class = "form-control"
                                                                format = "MM/dd/yyyy"
                                                                @input="changeServiceDate($event, i)"
                                                                v-model="e.datePicker"
                                                                ></datepicker>
                                                    </div>

                                                    <div v-else>
                                                        {{ dateFormat(e.expense.date) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Supplier -->
                                                    <div v-if="editExpense === i">
                                                        <select
                                                                :name="'supplier' + i"
                                                                :id="'supplier' + i"
                                                                class="form-control"
                                                                placeholder="Proveedor"
                                                                v-model="e.expense.supplier_id"
                                                                @change="changeSupplier(i)"
                                                                >
                                                            <option
                                                                    v-for="supplier in suppliers"
                                                                    :value="supplier.id"
                                                                    :key="supplier.id"
                                                                    >
                                                                {{ supplier.name }}
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div v-else>
                                                        {{ e.supplier.name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Description -->
                                                    <div v-if="editExpense === i">
                                                        <input
                                                                type="text"
                                                                :id="'description' + i"
                                                                :name="'description' + i"
                                                                class="form-control"
                                                                v-model="e.expense.description"
                                                                :class="{'input-error': errors.has('description' + i)}"
                                                                placeholder="Descripcion"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                >
                                                        <p class="error" v-if="errors.firstByRule('description' + i, 'required')">
                                                            Requerido
                                                        </p>
                                                    </div>

                                                    <div v-else>
                                                        {{ e.expense.description }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Patient -->
                                                    <div v-if="editExpense === i">
                                                        <div
                                                                class="input-group"
                                                                v-if="e.expense.supplier && (e.supplier.type === 8 || e.supplier.type === 9 || e.supplier.type === 6)"
                                                                >
                                                            <input  type="text"
                                                                    class="form-control"
                                                                    :id="'patient' + i"
                                                                    :name="'patient' + i"
                                                                    placeholder="Paciente"
                                                                    readonly
                                                                    v-model="e.patient.name"
                                                                    v-validate
                                                                    data-vv-rules="required"
                                                                    :class="{'input-error': errors.has('patient' + i)}"
                                                                    >
                                                            <span class="input-group-btn">
                                                                <button
                                                                        class="btn btn-info"
                                                                        type="button"
                                                                        data-toggle="modal"
                                                                        data-target="#patientModal"
                                                                        @click="searchPatients(); selectedPatientExpense = i"
                                                                        >
                                                                    <i class="glyphicon glyphicon-search"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div v-else>
                                                            No aplica
                                                        </div>
                                                        <p class="error" v-if="errors.firstByRule('patient' + i, 'required')">
                                                            Requerido
                                                        </p>
                                                    </div>

                                                    <div v-else>
                                                        {{ e.patient.name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Servicio -->
                                                    <div v-if="editExpense === i">
                                                        <div
                                                                class="input-group"
                                                                v-if="e.patient.public_id"
                                                                >
                                                            <input  type="text"
                                                                    class="form-control"
                                                                    :id="'service' + i"
                                                                    :name="'service' + i"
                                                                    placeholder="Servicio"
                                                                    readonly
                                                                    v-model="e.expense.patient_history.public_id"
                                                                    v-validate
                                                                    data-vv-rules="required"
                                                                    :class="{'input-error': errors.has('service' + i)}"
                                                                    >
                                                        <span class="input-group-btn">
                                                            <button
                                                                    class="btn btn-danger"
                                                                    type="button"
                                                                    v-if="e.expense.patient_history_id"
                                                                    @click="e.expense.patient_history = {};e.expense.patient_history_id = null;e.patient = {}"
                                                                    >
                                                                <i class="glyphicon glyphicon-remove"></i>
                                                            </button>
                                                            <button
                                                                    class="btn btn-info"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#serviceModal"
                                                                    @click="selectedServiceExpense = i; searchServices()"
                                                                    >
                                                                <i class="glyphicon glyphicon-search"></i>
                                                            </button>
                                                        </span>
                                                        </div>
                                                        <div v-else>
                                                            Sin paciente
                                                        </div>
                                                        <p class="error" v-if="errors.firstByRule('service' + i, 'required')">
                                                            Requerido
                                                        </p>
                                                    </div>

                                                    <div v-else>
                                                        {{
                                                            e.expense.patient_history && e.expense.patient_history.product ?
                                                                e.expense.patient_history.product.name :
                                                                ''
                                                        }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Doctor -->
                                                    <div v-if="editExpense === i">
                                                        {{ e.doctor.name }}
                                                    </div>

                                                    <div v-else>
                                                        {{ e.doctor.name }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Amount -->
                                                    <div v-if="editExpense === i">
                                                        <input
                                                                type="number"
                                                                :id="'amount' + i"
                                                                :name="'amount' + i"
                                                                class="form-control"
                                                                v-model="e.expense.amount"
                                                                :class="{'input-error': errors.has('amount' + i)}"
                                                                placeholder="Monto"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                >
                                                        <p class="error" v-if="errors.firstByRule('amount' + i, 'required')">
                                                            Requerido
                                                        </p>
                                                    </div>

                                                    <div v-else>
                                                        {{ e.expense.amount }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <!-- Editar -->
                                                    <button
                                                            class="btn btn-warning"
                                                            @click="editExpense = i"
                                                            v-if="editExpense !== i"
                                                            :disabled="editExpense !== null"
                                                            >
                                                        <i class="glyphicon glyphicon-edit"></i>
                                                    </button>

                                                    <!-- Cancelar -->
                                                    <button
                                                            class="btn btn-warning"
                                                            @click="editExpense = null"
                                                            v-if="editExpense === i"
                                                            >
                                                        <i class="glyphicon glyphicon-remove-sign"></i>
                                                    </button>
                                                </td>
                                                <td>
                                                    <!-- Eliminar -->
                                                    <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#deleteModal"
                                                            v-if="editExpense !== i"
                                                            @click="deleteExpense = e.expense.id"
                                                            >
                                                        <i class="glyphicon glyphicon-remove"></i>
                                                    </button>

                                                    <!-- Guardar -->
                                                    <button
                                                            class="btn btn-success"
                                                            v-if="editExpense === i"
                                                            :disabled="loading"
                                                            @click="validateExpense(e.expense)"
                                                            >
                                                        <i class="glyphicon glyphicon-ok"></i>
                                                    </button>
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
                                    <tr v-for="p in modal.data" :key="p.id">
                                        <td>{{ p.phone }}</td>
                                        <td>{{ p.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    data-dismiss="modal"
                                                    @click="selectPatient(p)"
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
                        <button type="button" id="closeModalPatient" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="serviceModal" tabindex="-1" role="dialog" aria-labelledby="serviceModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>
                                    <strong>
                                        Selecciona servicio
                                    </strong>
                                </h3>
                            </div>

                            <div class="col-xs-12">
                                <datepicker
                                        name = "serviceDate"
                                        id = "serviceDate"
                                        language="es"
                                        input-class = "form-control datepicker"
                                        format = "MM/dd/yyyy"
                                        v-model="modalService.datePicker"
                                        @input="changeDateService($event)"
                                        ></datepicker>
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-xs-12">

                                <table class="table table-responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th width="30%">Código</th>
                                        <th width="50%">Producto</th>
                                        <th width="20%">Precio</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="! modalService.loading">
                                    <tr v-for="s in modalService.data" :key="s.id">
                                        <td>{{ s.public_id }}</td>
                                        <td>{{ s.product.name }}</td>
                                        <td>{{ '$' + s.price }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectService(s)"
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
                        <button  type="button"
                                 id="closeServiceModal"
                                 class="btn btn-secondary"
                                 data-dismiss="modal"
                                >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>¿Esta seguro de eliminar este gasto?</h4>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-show="! loading"
                                @click="deleteExpense = null"
                                id="closeDeleteModal">
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                @click="sendDelete()"
                                v-show="! loading">
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
    import RegisterExpenseModal from './RegisterExpenseModal';

    export default {
        components: {
            Datepicker,
            RegisterExpenseModal
        },
        props: {
            suppliers: {
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
                  type: 0,
                  expenses: []
                },
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                },
                modalService: {
                    data: [],
                    loading: false,
                    datePicker: new Date(),
                    date: null
                },
                editExpense: null,
                selectedPatientExpense: null,
                selectedServiceExpense: null,
                deleteExpense: null
            }
        },
        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.start = year + '-' + month + '-' + day;
            this.data.end = year + '-' + month + '-' + day;

            this.changeDateService(this.modalService.datePicker);
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
                    if (err.response.status === 403 || err.response.status === 405) {
                        location.href = '/';
                    }
                    this.modal.loading = false;
                })
                ;
            },

            selectPatient: function (patient) {
                this.data.expenses[ this.selectedPatientExpense].patient = patient;
                this.data.expenses[ this.selectedPatientExpense].expense.patient_id = patient.id;
                this.data.expenses[ this.selectedPatientExpense].expense.patient_history_id = null;
                this.data.expenses[ this.selectedPatientExpense].expense.patient_history = {};
                this.errors.remove('patient' + this.selectedPatientExpense);

                this.selectedPatientExpense = null;
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

            search: function () {
                this.loading = true;
                this.editExpense = null;

                axios.get(
                        '/admin/report/expensesData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&type=' + this.data.type
                )
                    .then((res) => {
                        this.loading = false;

                        this.data.expenses = [];

                        if (res.data.success) {

                            let date;
                            let datePicker;

                            res.data.expenses.forEach((expense) => {

                                date = expense.date.split(' ');
                                date = date[0].split('-');

                                datePicker = new Date();
                                datePicker.setFullYear(date[0], parseInt(date[1]) - 1, date[2]);

                                let doctor = {};

                                if (expense.doctor_commission) {
                                    doctor = expense.doctor_commission;
                                }

                                if (expense.patient_history) {
                                    doctor = expense.patient_history.doctor;
                                }

                                this.data.expenses.push({
                                    expense: expense,
                                    patient: expense.patient_history ? expense.patient_history.patient : {},
                                    doctor: doctor,
                                    supplier: expense.supplier,
                                    datePicker: datePicker
                                });
                            });
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.expenses = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            getTotal: function () {
                let total = 0;

                this.data.expenses.forEach((expense) => {
                    total += expense.expense.amount;
                });

                return total;
            },

            changeSupplier: function (expenseIndex) {
                const supplierId = this.data.expenses[expenseIndex].expense.supplier_id;

                for (let i in this.suppliers) {
                    if (this.suppliers[i].id === supplierId) {

                        this.data.expenses[expenseIndex].supplier = this.suppliers[i];

                        if (this.suppliers[i].type !== 8 && this.suppliers[i].type !== 9 && this.suppliers[i].type !== 6) {

                            this.data.expenses[expenseIndex].patient = {};
                            this.data.expenses[expenseIndex].expense.patient_id = null;
                            this.data.expenses[expenseIndex].expense.patient_history = {};
                            this.data.expenses[expenseIndex].expense.patient_history_id = null;
                        }
                    }
                }
            },

            changeDateService: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.modalService.date = year + '-' + month + '-' + day;

                this.searchServices();
            },

            searchServices: function () {

                if (this.selectedServiceExpense === null) {
                    return false;
                }

                this.modalService.data = [];
                this.modalService.loading = true;

                const public_id = this.data.expenses[ this.selectedServiceExpense ].patient.public_id;

                axios.get('/user/service/' + public_id + '/search?start=' + this.modalService.date + '&end=' + this.modalService.date)
                        .then((res) => {
                    this.modalService.loading = false;

                    for (let x in res.data.data) {

                        for (let y in res.data.data[x].services) {

                            this.modalService.data.push(res.data.data[x].services[y]);
                        }
                    }
                })
                .catch((err) => {
                    if (err.response.status === 403 || err.response.status === 405) {
                        location.href = '/';
                    }
                    this.modalService.loading = false;
                })
                ;
            },

            selectService: function (service) {
                this.data.expenses[ this.selectedServiceExpense ].expense.patient_history = service;
                this.data.expenses[ this.selectedServiceExpense ].expense.patient_history_id = service.id;
                this.errors.remove('service' + this.selectedServiceExpense);
                this.selectedServiceExpense = null;

                $('#closeServiceModal').click();
            },

            validateExpense: function (expense) {
                this.$validator.validateAll().then((res) => {

                    if (res) {
                        this.updateExpense(expense);
                    }
                })
            },

            updateExpense: function (expense) {

                axios.put('/user/expense/' + expense.id, expense)
                    .then((res) => {
                        if (res.data.success) {
                            this.search();
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                    })
                ;
            },

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/expense/' + this.deleteExpense)
                        .then((res) => {

                    if (res.data.success) {
                        $('#closeDeleteModal').click();
                        this.search();
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

            changeServiceDate: function (date, index) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.expenses[index].expense.date = year + '-' + month + '-' + day;
            },

        }
    }
</script>