<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Registrar gastos
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
                                        <table class="table table-responsive">
                                            <thead>
                                            <tr>
                                                <th width="15%">Fecha</th>
                                                <th width="15%">Proveedor</th>
                                                <th width="17.5%">Paciente</th>
                                                <th width="17.5%">Servicio</th>
                                                <th width="20%">Gasto</th>
                                                <th width="15%">Monto</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(expense, id) in expenses">
                                                <td>
                                                    <datepicker
                                                            name = "end"
                                                            id = "end"
                                                            language="es"
                                                            input-class = "form-control datepicker"
                                                            format = "MM/dd/yyyy"
                                                            @input="changeDate($event, id)"
                                                            v-model="expense.picker"
                                                            :disabled="disabledDates"
                                                            ></datepicker>
                                                </td>
                                                <td>
                                                    <select
                                                            :name="'supplier' + id"
                                                            :id="'supplier' + id"
                                                            class="form-control"
                                                            placeholder="Proveedor"
                                                            v-model="expense.supplier_id"
                                                            @change="changeSupplier(id)"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('supplier' + id)}"
                                                            >
                                                        <option
                                                                v-for="supplier in suppliers"
                                                                :value="supplier.id"
                                                                >
                                                            {{ supplier.name }}
                                                        </option>
                                                    </select>

                                                    <p class="error" v-if="errors.firstByRule('supplier' + id, 'required')">
                                                        Requerido
                                                    </p>
                                                </td>
                                                <td>
                                                    <div
                                                            class="input-group"
                                                            v-if="expense.supplier && (expense.supplier.type === 7 || expense.supplier.type === 8 || expense.supplier.type === 9 || expense.supplier.type === 6)"
                                                        >
                                                        <input  v-if="expense.supplier.type !== 7"
                                                                type="text"
                                                                class="form-control"
                                                                :id="'patient' + id"
                                                                :name="'patient' + id"
                                                                placeholder="Paciente"
                                                                readonly
                                                                v-model="expense.patient.name"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                :class="{'input-error': errors.has('patient' + id)}"
                                                            >

                                                        <input  v-else
                                                                type="text"
                                                                class="form-control"
                                                                :id="'patient' + id"
                                                                :name="'patient' + id"
                                                                placeholder="Paciente"
                                                                readonly
                                                                v-model="expense.patient.name"
                                                        >
                                                        <span class="input-group-btn">
                                                            <button
                                                                    class="btn btn-info"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#patientModal"
                                                                    @click="searchPatients(); selectedPatientExpense = id"
                                                                >
                                                                <i class="glyphicon glyphicon-search"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div v-else>
                                                        No aplica
                                                    </div>
                                                    <p class="error" v-if="errors.firstByRule('patient' + id, 'required')">
                                                        Requerido
                                                    </p>
                                                </td>
                                                <td>
                                                    <div
                                                            class="input-group"
                                                            v-if="expense.patient.public_id"
                                                        >
                                                        <input  type="text"
                                                                class="form-control"
                                                                :id="'service' + id"
                                                                :name="'service' + id"
                                                                placeholder="Servicio"
                                                                readonly
                                                                v-model="expense.patient_history.public_id"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                :class="{'input-error': errors.has('service' + id)}"
                                                            >
                                                        <span class="input-group-btn">
                                                            <button
                                                                    class="btn btn-info"
                                                                    type="button"
                                                                    data-toggle="modal"
                                                                    data-target="#serviceModal"
                                                                    @click="selectedServiceExpense = id; searchServices()"
                                                                >
                                                                <i class="glyphicon glyphicon-search"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                    <div v-else>
                                                        Sin paciente
                                                    </div>
                                                    <p class="error" v-if="errors.firstByRule('service' + id, 'required')">
                                                        Requerido
                                                    </p>
                                                </td>
                                                <td>
                                                    <input  type="text"
                                                            class="form-control"
                                                            placeholder="Descripción"
                                                            :name="'description' + id"
                                                            :id="'description' + id"
                                                            maxlength="255"
                                                            v-model="expense.description"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('description' + id)}"
                                                            >

                                                    <p class="error" v-if="errors.firstByRule('description' + id, 'required')">
                                                        Requerido
                                                    </p>
                                                </td>
                                                <td>
                                                    <input  type="number"
                                                            class="form-control"
                                                            placeholder="Monto"
                                                            :name="'amount' + id"
                                                            :id="'amount' + id"
                                                            v-model="expense.amount"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('amount' + id)}"
                                                            >

                                                    <p class="error" v-if="errors.firstByRule('amount' + id, 'required')">
                                                        Requerido
                                                    </p>
                                                </td>
                                                <td class="text-center">
                                                    <a @click="remove()">X</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="6">
                                                    <button class="btn btn-success" @click="add()">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        Agregar
                                                    </button>
                                                </td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 text-center">
                                        <button class="btn btn-primary btn-lg"
                                                @click="validate()"
                                                v-if="!loading"
                                                >
                                            <i class="glyphicon glyphicon-check"></i>
                                            Guardar
                                        </button>

                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </section>

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
                                                        <tr v-for="p in modal.data">
                                                            <td>{{ p.phone }}</td>
                                                            <td>{{ p.name }}</td>
                                                            <td>
                                                                <button
                                                                        class="btn btn-primary"
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
                                                        <tr v-for="s in modalService.data">
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

                        </div>
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
                expenses: [],
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
                selectedPatientExpense: null,
                selectedServiceExpense: null,
                disabledDates: {}
            }
        },

        mounted: function () {
            this.changeDateService(this.modalService.datePicker);

            const today = new Date();
            const yesterday = new Date(today.getTime() - 24*60*60*1000);
            this.disabledDates = this.user.hasRole.admin ? {} : {to: yesterday}
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
                this.expenses[ this.selectedPatientExpense].patient = patient;
                this.expenses[ this.selectedPatientExpense].patient_id = patient.id;
                this.expenses[ this.selectedPatientExpense].patient_history_id = null;
                this.expenses[ this.selectedPatientExpense].patient_history = {};
                this.errors.remove('patient' + this.selectedPatientExpense);

                $('#closeModalPatient').click();

                this.selectedPatientExpense = null;
            },

            changeDate: function (date, index) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.expenses[index].date = year + '-' + month + '-' + day;
            },

            add: function () {
                const index = this.expenses.length;

                this.expenses.push({
                    patient_id: null,
                    patient: {},
                    supplier_id: null,
                    supplier: null,
                    description: null,
                    date: null,
                    picker: null,
                    amount: null,
                    patient_history_id: null,
                    patient_history: {}
                });

                this.expenses[index].picker = new Date();
                this.changeDate(this.expenses[index].picker, index);
            },

            remove: function (index) {
                if (this.expenses.length > 1) {
                    this.expenses.splice(index, 1);
                }
            },

            validate: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.post('/user/expense', this.expenses)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
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

                const public_id = this.expenses[ this.selectedServiceExpense ].patient.public_id;

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
                this.expenses[ this.selectedServiceExpense ].patient_history = service;
                this.expenses[ this.selectedServiceExpense ].patient_history_id = service.id;
                this.errors.remove('service' + this.selectedServiceExpense);
                this.selectedServiceExpense = null;

                $('#closeServiceModal').click();
            },

            changeSupplier: function (expenseIndex) {
                const supplierId = this.expenses[expenseIndex].supplier_id;

                for (let i in this.suppliers) {
                    if (this.suppliers[i].id === supplierId) {

                        this.expenses[expenseIndex].supplier = this.suppliers[i];

                        if (this.suppliers[i].type !== 8 && this.suppliers[i].type !== 9 && this.suppliers[i].type !== 6) {

                            this.expenses[expenseIndex].patient = {};
                            this.expenses[expenseIndex].patient_id = null;
                            this.expenses[expenseIndex].patient_history = {};
                            this.expenses[expenseIndex].patient_history_id = null;
                        }
                    }
                }
            }
        }
    }
</script>