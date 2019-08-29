<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Actualizar proveedor
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
                                                    placeholder="Telefono del proveedor"
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
                                                    placeholder="Nombre del proveedor"
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
                                                    placeholder="Email del proveedor"
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
                                            <label for="type">Tipo</label>
                                            <select
                                                    name="type"
                                                    id="type"
                                                    class="form-control"
                                                    v-model="form.type"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    :class="{'input-error': errors.has('type')}"
                                                    >
                                                <option value="1">Oficina</option>
                                                <option value="2">Servicios generales</option>
                                                <option value="3">RRHH</option>
                                                <option value="4">Mantenimiento</option>
                                                <option value="5">Farmacia</option>
                                                <option value="6">Implantes</option>
                                                <option value="7">Deposito dental</option>
                                                <option value="8">Laboratorio</option>
                                                <option value="9">Imagenes</option>
                                            </select>
                                            <p class="error" v-if="errors.firstByRule('type', 'required')">
                                                Campo requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <button class="btn btn-warning" v-bind:disabled="loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Actualizar proveedor
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
                                            Eliminar proveedor
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <h4>¿Esta seguro de eliminar este proveedor?</h4>
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
        </div>
    </section>
</template>

<script>
    export default {
        props: {
            viewData: {
                type: Object,
                required: true
            },
            authUser: {
                type: Object,
                required: true
            }
        },
        data: function () {
            return {
                loading: false,
                phoneError: false,
                form: this.viewData
            }
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/user/supplier/' + this.form.public_id, this.form)
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

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/supplier/' + this.form.public_id)
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