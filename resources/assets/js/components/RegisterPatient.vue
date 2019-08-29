<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Registrar paciente
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
                                                    :disabled="phoneError"
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
                                                    :disabled="phoneError"
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

                                <div class="row" v-if="! phoneError">
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
                                                        :key="reference.id"
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

                                    <div class="col-sm-4" v-if="user.hasRole.sell_manager">
                                        <div class="form-group">
                                            <label for="cancel_appointment">Registrar llamada</label>

                                            <p>
                                                <input
                                                        type="checkbox"
                                                        name="register_call"
                                                        id="register_call"
                                                        v-model="form.register_call"
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12" v-if="! phoneError">
                                        <button class="btn btn-success" v-bind:disabled="loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Registar paciente
                                        </button>
                                    </div>

                                    <div class="col-xs-12" v-if="phoneError">
                                        <a class="btn btn-primary"
                                            :href="'/user/service/' + patient.public_id + '/edit'">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar servicio
                                        </a>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a data-toggle="modal" id="patientModalButton" data-target="#patientModal" class="hidden"></a>
        <!-- Modal -->
        <div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <p>
                                <strong>ATENCIÓN:</strong> El paciente <strong>{{ form.name }}</strong> ya se encuentra
                                registrado. Puede indicar los servicios prestados sin necesidad de repetir este paso
                            </p>
                            <p>
                                ¿Desea registrar servicios a este paciente?
                            </p>
                        </div>

                        <a 
                            class="btn btn-success"
                            v-if="patient"
                            :href="'/user/service/' + patient.public_id + '/edit'"
                        >
                            <i class="glyphicon glyphicon-plus"></i>
                            SI
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
                    </div>
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
            user: {
                type: Object,
                required: true
            }
        },

        data: function () {
            return {
                loading: false,
                phoneError: false,
                form: {
                    name: '',
                    phone: '',
                    email: '',
                    patient_reference_id: null,
                    cancel_appointment: false,
                    register_call: false
                },
                patient: null
            }
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.loading = true;
                        this.verifyPhone(true)
                    }
                });
            },

            verifyPhone: function (sendForm = false) {
                if (this.form.phone.length < 5) {
                    return false
                }

                axios.get('/user/patient/phone/' + this.form.phone)
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
                            this.patient = res.data.patient;
                            this.form.name = res.data.patient.name;
                            this.form.email = res.data.patient.email;

                            if (this.phoneError) {
                                $('#patientModalButton').click();
                            }
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

                axios.post('/user/patient', this.form)
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

<style scoped>
    .patientModal {
        font-size: 25px;
    }
</style>
