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
                                    <div class="col-xs-12">
                                        <table class="table table-responsive">
                                            <thead>
                                            <tr>
                                                <th width="17%">Fecha</th>
                                                <th width="23%">Proveedor</th>
                                                <th width="40%">Gasto</th>
                                                <th width="20%">Monto</th>
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
                                                            ></datepicker>
                                                </td>
                                                <td>
                                                    <select
                                                            :name="'supplier' + id"
                                                            :id="'supplier' + id"
                                                            class="form-control"
                                                            placeholder="Proveedor"
                                                            v-model="expense.supplier_id"
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
                                                <td colspan="5">
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
            }
        },
        data: function () {
            return {
                loading: false,
                expenses: [],
                patient: null,
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                },
            }
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
                    this.modal.loading = false;
                })
                ;
            },

            selectPatient: function (patient) {
                this.patient = patient;
                this.expenses = [{
                    patient_id: patient.id,
                    supplier_id: null,
                    description: null,
                    date: null,
                    picker: null,
                    amount: null
                }];

                this.expenses[0].picker = new Date();
                this.changeDate(this.expenses[0].picker, 0);
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
                    patient_id: this.patient.id,
                    supplier_id: null,
                    description: null,
                    date: null,
                    picker: null,
                    amount: null
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
                        console.log(err);
                        this.loading = false;
                    })
                ;
            }
        }
    }
</script>