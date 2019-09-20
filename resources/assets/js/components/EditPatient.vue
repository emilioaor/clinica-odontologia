<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Actualizar paciente
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form
                                    novalidate
                                    @submit.prevent="validateForm()"
                                    >

                                <div class="row">
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
                                                    @keyup="verifyPhone()"
                                                    :class="{'input-error': errors.has('phone') || phoneError}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('phone', 'required')">
                                                Campo requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('phone', 'regex')">
                                                Formato invalido
                                            </p>
                                            <p class="error" v-if="! errors.has('phone') && phoneError">
                                                Este paciente ya esta registrado
                                            </p>
                                        </div>
                                    </div>

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

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Referencia</label>

                                            <select
                                                    name="patient_reference_id"
                                                    id="patient_reference_id"
                                                    class="form-control"
                                                    v-model="form.patient_reference_id"
                                                    >
                                                <option :value="null">Ninguno</option>
                                                <option
                                                        v-for="reference in patientReferences"
                                                        :value="reference.id"
                                                        >
                                                    {{ reference.description }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="cancel_appointment">Bloquear citas</label>

                                            <p>
                                                <input
                                                        type="checkbox"
                                                        name="cancel_appointment"
                                                        id="cancel_appointment"
                                                        v-model="form.cancel_appointment"
                                                        >
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <button class="btn btn-warning" v-bind:disabled="loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Actualizar paciente
                                        </button>

                                        <button
                                                type="button"
                                                class="btn btn-danger"
                                                data-toggle="modal"
                                                data-target="#deleteModal"
                                                v-bind:disabled="loading"
                                                v-if="authUser.hasRole.admin"
                                                >
                                            <i class="glyphicon glyphicon-remove"></i>
                                            Eliminar paciente
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <h4>¿Esta seguro de eliminar este paciente?</h4>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button
                                                                type="button"
                                                                class="btn btn-secondary"
                                                                data-dismiss="modal"
                                                                v-if="! loading">
                                                            NO
                                                        </button>
                                                        <button
                                                                type="button"
                                                                class="btn btn-danger"
                                                                @click="sendDelete()"
                                                                v-if="! loading">
                                                            SI
                                                        </button>

                                                        <img src="/img/loading.gif" v-if="loading">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de cotizaciones -->
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list"></i>
                        Lista de cotizaciones
                    </h1>
                </div>
            </div>
            <div class="row edit-patient__budgets">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <div class="row">

                                <div class="col-sm-3" v-for="budget in form.budgets.data" v-if="form.budgets.data.length > 0">

                                    <div class="panel panel-info">
                                        <div class="panel-body">
                                            <h4>
                                                #{{ budget.public_id }}
                                            </h4>
                                        </div>
                                        <div class="panel-footer">
                                            <h5>
                                                <strong>Monto:</strong><br>
                                                ${{ budget.total_footer_value }} USD
                                            </h5>
                                            <h5>
                                                <strong>Generada:</strong><br>
                                                {{ budget.created_at | date }}
                                            </h5>
                                            <div class="text-center">
                                                <a
                                                        :href="'/user/budget/' + budget.public_id + '/edit'"
                                                        class="btn btn-info"
                                                    >
                                                    <i class="glyphicon glyphicon-eye-open"></i>
                                                    Ver detalle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12" v-if="form.budgets.data.length === 0">
                                    <p>
                                        No se ha generado cotizaciones.
                                        <a href="/user/budget/create">
                                            Generar cotización
                                        </a>
                                    </p>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xs-12" v-if="pagination.total > pagination.per_page">
                    <ul class="pagination">
                        <li :class="{'disabled': pagination.current === 1}">
                            <a :href="pagination.path + '?page=1'" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <li
                                v-for="i in range(1, pagination.last_page)"
                                :class="{'active': i === pagination.current}"
                            >
                            <a :href="pagination.path + '?page=' + i">
                                {{ i }}
                            </a>
                        </li>


                        <li :class="{'disabled': pagination.current === pagination.last_page}">
                            <a :href="pagination.path + '?page=' + pagination.last_page" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props: {
            patientReferences: {
                type: Array,
                required: true
            },
            patient: {
                type: Object,
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
                phoneError: false,
                form: {},
                pagination: {
                    current: null,
                    from: null,
                    to: null,
                    path: null,
                    last_page: null,
                    total: null,
                    per_page: null
                },
                authUser: this.user
            }
        },

        beforeMount: function () {
            this.form = this.patient;
            this.generatePagination();
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.loading = true;
                        this.verifyPhone(true);
                    }
                });
            },

            verifyPhone: function (sendForm = false) {
                if (this.form.phone.length < 5) {
                    return false
                }

                axios.get('/user/patient/phone/' + this.form.phone + '/' + this.form.public_id)
                    .then((res) => {
                        if (res.data.success) {

                            if (! res.data.phoneError) {
                                this.phoneError = false;

                                if (sendForm) {
                                    this.sendForm()
                                }

                                if (res.data.isPatient) {
                                    return
                                }
                            }

                            this.loading = false;
                            this.phoneError = res.data.phoneError;
                            /*this.patient = res.data.patient;
                            this.form.name = res.data.patient.name;
                            this.form.email = res.data.patient.email;*/
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.loading = false;
                        this.phoneError = false;
                    })
                ;
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/user/patient/' + this.form.public_id, this.form)
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
            },

            generatePagination: function () {
                this.pagination.current = this.form.budgets.current_page;
                this.pagination.from = this.form.budgets.from;
                this.pagination.to = this.form.budgets.to;
                this.pagination.path = this.form.budgets.path;
                this.pagination.last_page = this.form.budgets.last_page;
                this.pagination.total = this.form.budgets.total;
                this.pagination.per_page = this.form.budgets.per_page;
            },

            range: function (start, end) {
                let array = [];

                for (let x = start; x <= end; x++) {
                    array.push(x);
                }

                return array;
            },

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/patient/' + this.form.public_id)
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
                        console.log(err);
                    })
            }
        },
        filters: {
            date: function (value) {
                let date = value.split(' ');
                date = date[0].split('-');

                date = date[1] + '/' + date[2] + '/' +date[0];

                return date;
            }
        }
    }
</script>