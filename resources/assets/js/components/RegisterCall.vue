<template>
    <section class="edit-service">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Registrar llamada
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
                                            <label for="user">Secretaria</label>
                                            <select
                                                    name="user"
                                                    id="user"
                                                    class="form-control"
                                                    v-model="form.user_id"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    :class="{'input-error': errors.has('user')}"
                                                    >
                                                <option
                                                        v-for="user in users"
                                                        :key="user.id"
                                                        :value="user.id"
                                                        >
                                                    {{ user.name }}
                                                </option>
                                            </select>
                                            <p class="error" v-if="errors.firstByRule('user', 'required')">
                                                Requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">

                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="description">Razón de la llamada</label>
                                            <textarea
                                                    name="description"
                                                    id="description"
                                                    rows="4"
                                                    class="form-control"
                                                    v-model="form.description"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    :class="{'input-error': errors.has('description')}"
                                            ></textarea>
                                            <p class="error" v-if="errors.firstByRule('description', 'required')">
                                                Campo requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <button class="btn btn-success" @click="validateForm()" v-if="!loading">
                                            <i class="glyphicon glyphicon-phone-alt"></i>
                                            Registrar llamada
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">
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
                                    <tr v-for="p in modal.data" :key="p.id" v-if="!patient || patient.id !== p.id">
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
    </section>
</template>

<script>
    export default {
        props: {
            users: {
                type: Array,
                required: true
            }
        },

        data: function() {
            return {
                loading: false,
                patient: null,
                form: {
                    description: null,
                    patient_id: null,
                    user_id: null
                },
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                }
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

            selectPatient: function (patient) {
                this.patient = patient;
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

                axios.post('/user/callLog', this.form)
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
            }
        }
    }
</script>