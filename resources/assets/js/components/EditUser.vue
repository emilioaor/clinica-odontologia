<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Actualizar usuario
                    </h1>
                </div>
            </div>

            <div class="row">

                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form @submit.prevent="validateForm()">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="username">Usuario</label>
                                            <input
                                                    type="text"
                                                    name="username"
                                                    id="username"
                                                    class="form-control"
                                                    placeholder="Nombre de usuario"
                                                    v-model="form.username"
                                                    v-validate
                                                    maxlength="20"
                                                    data-vv-rules="required|min:5|regex:^[a-z]{1}[a-z0-9]+$"
                                                    data-vv-scope="data"
                                                    :class="{'input-error': errors.has('data.username')}"
                                                    disabled
                                                    >
                                            <p class="error" v-if="errors.firstByRule('username', 'required', 'data')">
                                                Este campo es requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('username', 'min', 'data')">
                                                Minimo 5 caracteres
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('username', 'regex', 'data')">
                                                Formato invalido
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="username">Nombre</label>
                                            <input
                                                    type="text"
                                                    name="name"
                                                    id="name"
                                                    class="form-control"
                                                    placeholder="Nombre"
                                                    v-model="form.name"
                                                    v-validate
                                                    maxlength="60"
                                                    data-vv-rules="required"
                                                    data-vv-scope="data"
                                                    :class="{'input-error': errors.has('name')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('name', 'required', 'data')">
                                                Este campo es requerido
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="username">Nivel de usuario</label>
                                            <select
                                                    name="level"
                                                    id="level"
                                                    class="form-control"
                                                    v-model="form.level"
                                                >
                                                <option value="1">Administrador</option>
                                                <option value="2">Doctor</option>
                                                <option value="3">Secretaria</option>
                                                <option value="4">Asistente</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4" v-if="form.level == 2">
                                        <div class="form-group">
                                            <label for="password">¿Externo?</label>
                                            <div>
                                                <input type="checkbox" v-model="form.external">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <img src="/img/loading.gif" v-if="loading">
                                        <button class="btn btn-warning" v-if="!loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Actualizar usuario
                                        </button>

                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteUserModal" v-if="!loading">
                                            <i class="glyphicon glyphicon-remove"></i>
                                            Eliminar usuario
                                        </button>


                                        <!-- Modal -->
                                        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <h4>¿Esta seguro de eliminar a este usuario?</h4>
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
                                                                @click="deleteUser()"
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

            <div class="row">

                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form @submit.prevent="validatePassForm()">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="password">Contraseña</label>
                                            <input
                                                    type="password"
                                                    name="password"
                                                    id="password"
                                                    class="form-control"
                                                    placeholder="Contraseña"
                                                    maxlength="20"
                                                    v-model="form.password"
                                                    v-validate
                                                    data-vv-rules="required|confirmed:password_confirmation"
                                                    data-vv-scope="pass"
                                                    :class="{'input-error': errors.has('pass.password')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('password', 'required', 'pass')">
                                                Este campo es requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('password', 'confirmed', 'pass')">
                                                Contraseñas no coinciden
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="password">Confirmar contraseña</label>
                                            <input
                                                    type="password"
                                                    name="password_confirmation"
                                                    id="password_confirmation"
                                                    class="form-control"
                                                    placeholder="Confirmar contraseña"
                                                    maxlength="20"
                                                    v-model="form.password_confirmation"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    data-vv-scope="pass"
                                                    :class="{'input-error': errors.has('pass.password_confirmation')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('password_confirmation', 'required', 'pass')">
                                                Este campo es requerido
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
        </div>
    </section>
</template>


<script>
    export default {
        props: ['user'],

        data: function () {
            return {
                loading: false,
                form: {}
            }
        },

        beforeMount: function () {
            this.form = JSON.parse(this.user);
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll('data').then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                })
            },

            validatePassForm: function () {
                this.$validator.validateAll('pass').then((res) => {
                    if (res) {
                        this.sendPassForm();
                    }
                })
            },

            sendForm : function () {
                this.loading = true;

                axios.put('/admin/user/' + this.form.public_id, this.form)
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
                ;
            },

            sendPassForm : function () {
                this.loading = true;

                axios.put('/admin/user/' + this.form.public_id + '/password', this.form)
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
                ;
            },

            deleteUser: function () {
                this.loading = true;

                axios.delete('/admin/user/' + this.form.public_id)
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
        }
    }
</script>