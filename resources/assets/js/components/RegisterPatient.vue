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
                                                    @keyup="phoneError = false"
                                                    :class="{'input-error': errors.has('phone') || phoneError}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('phone', 'required')">
                                                Campo requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('phone', 'regex')">
                                                Formato invalido
                                            </p>
                                            <p class="error" v-if="! errors.has('phone') && phoneError">
                                                Este telefono ya esta registrado
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
                                    <div class="col-xs-12">
                                        <button class="btn btn-success" v-bind:disabled="loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Registar paciente
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        data: function () {
            return {
                loading: false,
                phoneError: false,
                form: {
                    name: '',
                    phone: '',
                    email: ''
                }
            }
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.verifyPhone();
                    }
                });
            },

            verifyPhone: function () {
                this.loading = true;

                axios.get('/user/patient/phone/' + this.form.phone)
                    .then((res) => {
                        if (res.data.success) {

                            if (res.data.valid) {
                                this.sendForm();

                                return res
                            }

                            this.loading = false;
                            this.phoneError = true;
                        }
                    })
                    .catch((err) => {
    if (err.response.status === 403) {
        location.href = '/';
    }
                        this.loading = false;
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
    if (err.response.status === 403) {
        location.href = '/';
    }

                        this.loading = false;
                    })
                ;
            }
        }
    }
</script>