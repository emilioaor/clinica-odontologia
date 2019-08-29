<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-search" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Busqueda de gastos
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
                                               :user = "authUser"
                                               @register="search()"
                                        ></register-expense-modal>

                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="data.expenses">
                                <div class="col-xs-12">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Proveedor</th>
                                                <th>Servicio</th>
                                                <th>Gasto</th>
                                                <th>Monto</th>
                                                <th width="5%"></th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(expense, i) in data.expenses">
                                                <td>
                                                    <span v-if="editExpense !== i">
                                                        {{ dateFormat(expense.date) }}
                                                    </span>
                                                    <span v-else>

                                                        <datepicker
                                                                :name = "'dateExpense' + i"
                                                                :id = "'dateExpense' + i"
                                                                language="es"
                                                                input-class = "form-control"
                                                                format = "MM/dd/yyyy"
                                                                v-model="expense.datePicker"
                                                                @input="changeDateExpense($event, i)"
                                                                ></datepicker>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="editExpense !== i">
                                                        {{ expense.supplier.name }}
                                                    </span>
                                                    <span v-else>

                                                        <select
                                                                :name="'supplier' + i"
                                                                :id="'supplier' + i"
                                                                class="form-control"
                                                                v-model="expense.supplier_id"
                                                                @change="changeSupplier(i)"
                                                                >
                                                            <option
                                                                    v-for="supplier in suppliers"
                                                                    :value="supplier.id"
                                                                    >
                                                                {{ supplier.name }}
                                                            </option>
                                                        </select>

                                                    </span>
                                                </td>
                                                <td>{{ expense.patient_history ? expense.patient_history.public_id : ''  }}</td>
                                                <td>{{ expense.description }}</td>
                                                <td>{{ '$ ' + expense.amount  }}</td>
                                                <td>

                                                    <!-- Editar -->
                                                    <button
                                                            type="button"
                                                            class="btn btn-warning"
                                                            @click="editExpense = i"
                                                            v-if="editExpense !== i && editLoading !== i"
                                                            :disabled="editExpense !== i && editExpense !== null"
                                                            >
                                                        <i class="glyphicon glyphicon-edit"></i>
                                                    </button>

                                                    <!-- Cancelar -->
                                                    <button
                                                            type="button"
                                                            class="btn btn-warning"
                                                            @click="editExpense = null"
                                                            v-if="editExpense === i && editLoading !== i"
                                                            >
                                                        <i class="glyphicon glyphicon-remove-sign"></i>
                                                    </button>
                                                </td>
                                                <td>

                                                    <!-- Guardar -->
                                                    <button
                                                            type="button"
                                                            class="btn btn-success"
                                                            v-if="editExpense === i && editLoading !== i"
                                                            @click="saveExpense(i)"
                                                            >
                                                        <i class="glyphicon glyphicon-check"></i>
                                                    </button>

                                                    <!-- Eliminar -->
                                                    <button
                                                            type="button"
                                                            class="btn btn-danger"
                                                            data-toggle="modal"
                                                            data-target="#deleteModal"
                                                            v-if="authUser.hasRole.admin && editExpense !== i && editLoading !== i"
                                                            :disabled="editExpense !== i && editExpense !== null"
                                                            @click="deleteExpense = expense.id"
                                                            >
                                                        <i class="glyphicon glyphicon-remove"></i>
                                                    </button>

                                                    <!-- Loading -->
                                                    <img src="/img/loading.gif" v-if="editLoading === i">
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
    import RegisterExpenseModal from './RegisterExpenseModal.vue';

    export default {
        components: {
            Datepicker,
            RegisterExpenseModal
        },
        props: ['user', 'suppliers'],
        data: function () {
          return {
              loading: false,
              patient: null,
              initStart: new Date(),
              initEnd: new Date(),
              data: {
                  start: '',
                  end: '',
                  expenses: []
              },
              modal: {
                  data: [],
                  loading: false,
                  search: ''
              },
              authUser: JSON.parse(this.user),
              deleteExpense: null,
              editExpense: null,
              editLoading: null
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
                this.patient = patient;
                this.data.expenses = [];
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

            changeDateExpense: function (date, index) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.expenses[index].date = year + '-' + month + '-' + day;
            },

            search: function () {
                this.loading = true;

                axios.get('/user/expense/' + this.patient.public_id + '/search?start=' + this.data.start + '&end=' + this.data.end)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.expenses = res.data.expenses;

                            for (let i in this.data.expenses) {
                                this.data.expenses[i].datePicker = new Date(this.data.expenses[i].date);
                            }
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

            changeSupplier: function (index) {
                const supplierId = this.data.expenses[index].supplier_id;

                for (let i in this.suppliers) {
                    if (this.suppliers[i].id === supplierId) {
                        this.data.expenses[index].supplier = this.suppliers[i];
                    }
                }
            },

            saveExpense: function (index) {
                this.editLoading = index;
                const expense = this.data.expenses[index];

                axios.put('/user/expense/' + expense.id, expense)
                    .then((res) => {
                        this.editExpense = null;
                        this.editLoading = null;
                    })
                    .catch((err) => {
                        this.editLoading = null;
                        console.log(err);
                    })
            }
        }
    }
</script>