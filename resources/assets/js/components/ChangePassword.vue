<template>
    <section>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="alert alert-danger" v-if="errorList.length">
                            <ul>
                                <li v-for="error in errorList">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                        <form v-on:submit.prevent="validateForm()">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has_error': errors.has('current_password')}">
                                        <label for="current_password">Contraseña actual</label>
                                        <input
                                                type="password"
                                                class="form-control"
                                                placeholder="Contraseña actual"
                                                id="current_password"
                                                name="current_password"
                                                v-model="form.current_password"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:class="{'input-error': errors.has('current_password')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('current_password', 'required')">
                                            Campo requerido
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="current_password">Nueva contraseña</label>
                                        <input
                                                type="password"
                                                class="form-control"
                                                placeholder="Nueva contraseña"
                                                id="password"
                                                name="password"
                                                v-model="form.password"
                                                v-validate
                                                data-vv-rules="required|confirmed:password_confirmation"
                                                v-bind:class="{'input-error': errors.has('password')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('password', 'required')">
                                            Campo requerido
                                        </p>
                                        <p class="error" v-if="errors.firstByRule('password', 'confirmed')">
                                            Contraseñas no coinciden
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="current_password">Confirmar contraseña</label>
                                        <input
                                                type="password"
                                                class="form-control"
                                                placeholder="Confirmar contraseña"
                                                id="password_confirmation"
                                                name="password_confirmation"
                                                v-model="form.password_confirmation"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:class="{'input-error': errors.has('password_confirmation')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('password_confirmation', 'required')">
                                            Campo requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <img src="/img/loading.gif" v-if="loading">
                                    <button class="btn btn-success" v-if="!loading">
                                        <i class="glyphicon glyphicon-saved"></i>
                                        Cambiar contraseña
                                    </button>
                                </div>
                            </div>
                        </form>

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
                errorList: [],
                form: {
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                }
            }
        },

        methods: {
            validateForm: function () {
                this.errorList = false;

                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                })
            },

            sendForm: function () {
                this.loading = true;

                axios.put('config/changePassword', this.form)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        } else {
                            this.loading = false;

                            if (res.data.errors) {
                                this.errorList = res.data.errors;
                            }
                        }

                        return res;
                    })
                    .catch((err) => {
                        this.loading = false;

                        return err;
                    })
            }
        }
    }
</script>