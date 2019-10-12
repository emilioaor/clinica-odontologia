<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Registrar seguimiento
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
                                            v-if="! patient"
                                    >
                                        <i class="glyphicon glyphicon-search"></i>

                                        Seleccionar paciente
                                    </a>
                                    <a
                                            data-toggle="modal"
                                            data-target="#patientModal"
                                            @click="searchPatients()"
                                            v-if="patient"
                                    >
                                        <i class="glyphicon glyphicon-search"></i>

                                        Cambiar paciente
                                    </a>
                                    <hr>
                                </div>
                            </div>

                            <section v-if="patient">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Nombre del paciente"
                                                    id="name"
                                                    name="name"
                                                    v-model="form.name"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    :class="{'input-error': errors.has('name')}"
                                            >
                                            <p class="error" v-if="errors.firstByRule('name', 'required')">
                                                Campo requerido
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Telefono</label>
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Telefono del paciente"
                                                    id="phone"
                                                    name="phone"
                                                    v-model="form.phone"
                                                    v-validate
                                                    data-vv-rules="required|regex:^[0-9]{10}$"
                                                    maxlength="10"
                                                    :class="{'input-error': errors.has('phone')}"
                                            >
                                            <p class="error" v-if="errors.firstByRule('phone', 'required')">
                                                Campo requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('phone', 'regex')">
                                                Formato invalido
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Email</label>
                                            <input
                                                    type="email"
                                                    class="form-control"
                                                    placeholder="Email del paciente"
                                                    id="email"
                                                    name="email"
                                                    v-model="form.email"
                                                    v-validate
                                                    data-vv-rules="required|email"
                                                    :class="{'input-error': errors.has('email')}"
                                            >
                                            <p class="error" v-if="errors.firstByRule('email', 'required')">
                                                Campo requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('email', 'email')">
                                                Formato invalido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" v-if="user.hasRole.admin">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="secretary">Asignar a</label>
                                            <select
                                                    name="secretary"
                                                    id="secretary"
                                                    class="form-control"
                                                    v-model="form.secretary_id"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    :class="{'input-error': errors.has('secretary')}"
                                            >
                                                <option :value="0">- Sin asignar</option>
                                                <option
                                                        v-for="secretary in secretaries"
                                                        :key="secretary.id"
                                                        :value="secretary.id"
                                                >
                                                    {{ secretary.name }}
                                                </option>
                                            </select>
                                            <p class="error" v-if="errors.firstByRule('secretary', 'required')">
                                                Campo requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="note">Nota</label>
                                            <textarea
                                                    name="note"
                                                    id="note"
                                                    cols="30"
                                                    rows="4"
                                                    class="form-control"
                                                    v-model="form.note"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    :class="{'input-error': errors.has('note')}"
                                            ></textarea>

                                            <p class="error" v-if="errors.firstByRule('note', 'required')">
                                                Campo requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <button class="btn btn-success" v-bind:disabled="loading" @click="validateForm()">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Guardar
                                        </button>
                                    </div>
                                </div>
                            </section>
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
                                    <tr v-for="patient in modal.data">
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
    </section>
</template>

<script>
    export default {
        props: {
            secretaries: {
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
                form: {
                    name: '',
                    phone: '',
                    email: '',
                    note: '',
                    secretary_id: 0,
                    patient_id: null
                },
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                },
                patient: null
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
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.modal.loading = false;
                    })
                ;
            },

            selectPatient (patient) {
                this.patient = patient;
                this.form.name = patient.name;
                this.form.phone = patient.phone;
                this.form.email = patient.email;
                this.form.patient_id = patient.id;
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

                axios.post('/user/tracking', this.form)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }

                        this.loading = false;
                    })
                ;
            }
        }
    }
</script>