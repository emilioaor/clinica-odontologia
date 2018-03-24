<template>
    <div class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-pencil" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Servicios prestados
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
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

                                    Cambiar paciente
                                </a>
                                <hr>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">ID</label>
                                    <p>
                                        {{ data.public_id }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Paciente</label>
                                    <p>
                                        {{ data.name }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Telefono</label>
                                    <p>
                                        {{ data.phone }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <p>
                                        {{ data.email }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Fecha</label>
                                    <datepicker
                                            name = "date"
                                            id = "date"
                                            language="es"
                                            input-class = "form-control"
                                            format = "MM/dd/yyyy"
                                            v-model="initDate"
                                            @input="changeDate($event)"
                                            ></datepicker>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xs-12">
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="50%">Servicio</th>
                                            <th width="20%">Diente</th>
                                            <th width="20%" v-show="user.level === 1">Precio</th>
                                            <th width="10%"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(service, id) in services">
                                        <tr>
                                            <td>
                                                <select
                                                        :name="'product' + id"
                                                        :id="'product' + id"
                                                        class="form-control"
                                                        v-model="service.product_id"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('product' + id)}"
                                                        @change="changeProduct(service, id)"
                                                        :disabled="user.level !== 1 && service.doctor_id !== user.id"
                                                    >
                                                    <option
                                                            v-for="product in productList"
                                                            :value="product.id"
                                                            >
                                                        {{ product.name }}
                                                    </option>
                                                </select>
                                                <p class="error" v-if="errors.firstByRule('product' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <input
                                                        type="text"
                                                        :name="'tooth' + id"
                                                        :id="'tooth' + id"
                                                        class="form-control"
                                                        v-model="service.tooth"
                                                        :disabled="user.level !== 1 && service.doctor_id !== user.id"
                                                >
                                            </td>
                                            <td v-show="user.level === 1">
                                                <input
                                                        type="number"
                                                        class="form-control"
                                                        :name="'price' + id"
                                                        :id="'price' + id"
                                                        v-model="service.price"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('price' + id)}"
                                                        :disabled="!service.product_id || (user.level !== 1 && service.doctor_id !== user.id)"
                                                >
                                                <p class="error" v-if="errors.firstByRule('price' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <a @click="removeService(id)" v-if="user.level === 1 || service.doctor_id === user.id">
                                                    X
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button class="btn btn-success" @click="addService()">
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

                            <div class="col-xs-12">

                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Notas</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(note,id) in notes">
                                            <td>
                                                <textarea
                                                        cols="30"
                                                        rows="3"
                                                        class="form-control"
                                                        placeholder="Notas del paciente"
                                                        v-model="note.content"
                                                        ></textarea>
                                            </td>
                                            <td>
                                                <a @click="removeNote(id)">
                                                    X
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <button class="btn btn-success" @click="addNote()">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Nota
                                                </button>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <img src="/img/loading.gif" v-if="loading">
                                <button class="btn btn-primary btn-lg" @click="validateForm()" v-if="!loading">
                                    <i class="glyphicon glyphicon-check"></i>
                                    Guardar
                                </button>
                            </div>
                        </div>
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
                                    <tr v-for="patient in modal.data" v-if="patient.public_id !== data.public_id">
                                        <td>{{ patient.phone }}</td>
                                        <td>{{ patient.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectPatient(patient)"
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
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        props: ['patient', 'products', 'historyDate', 'currentUser'],
        components: {
            Datepicker
        },

        data: function () {
            return {
                loading: false,
                data: {},
                productList: [],
                services: [],
                date: '',
                initDate: '',
                notes: [],
                user: '',
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                }
            }
        },
        mounted: function () {
            this.data = JSON.parse(this.patient);
            this.productList = JSON.parse(this.products);
            this.services = this.data.patient_history;
            this.user = JSON.parse(this.currentUser);

            let date = this.historyDate.split('-');
            date = new Date(parseInt(date[0]), parseInt(date[1]) - 1, parseInt(date[2]));

            this.initDate = date;
            this.setDate(date)

            this.notes = this.data.notes;
        },

        methods: {
            addService: function () {
                this.services.push({
                    tooth: null,
                    product_id: null,
                    price: null,
                    doctor_id: this.user.id
                });
            },

            addNote: function () {
                this.notes.push({
                    content: ''
                });
            },

            removeService: function (index) {
                this.services.splice(index, 1);
            },

            removeNote: function (index) {
                this.notes.splice(index, 1);
            },

            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/user/service/' + this.data.public_id, {
                    services: this.services,
                    date: this.date,
                    notes: this.notes
                })
                    .then((res) => {
                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                        this.loading = false;
                    })
            },

            range: function (start, end) {
                let array = [];

                for (let x = start; x <= end; x++) {
                    array.push(x);
                }

                return array;
            },

            setDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.date = year + '-' + month + '-' + day;
            },

            changeDate: function (date) {
                this.loading = true;

                this.setDate(date);
                location.href = location.pathname + '?date=' + this.date;
            },

            changeProduct: function (service, index) {
                for (let i in this.productList) {
                    if (this.productList[i].id == service.product_id) {
                        this.services[index].price = this.productList[i].price;
                    }
                }
            },

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
                location.href = '/user/service/' + patient.public_id + '/edit?date=' + this.date;
            }
        }
    }
</script>