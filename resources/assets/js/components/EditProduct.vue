<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    {{ public_id }}
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form v-on:submit.prevent="validateForm()">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('public_id') || unique}">
                                        <label for="name">Código</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                id="public_id"
                                                name="public_id"
                                                placeholder="Codigo del producto"
                                                maxlength="30"
                                                v-model="form.public_id"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:disabled="loading"
                                                v-bind:class="{'input-error': errors.has('public_id') || unique}"
                                                @keyup="unique = false"
                                                >
                                        <p class="error" v-if="errors.firstByRule('public_id', 'required')">
                                            Este campo es requerido
                                        </p>
                                        <p class="error" v-if="! errors.has('public_id') && unique">
                                            Este código ya esta siendo usado
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('name')}">
                                        <label for="name">Nombre del producto</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                placeholder="Nombre del producto"
                                                maxlength="255"
                                                v-model="form.name"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:disabled="loading"
                                                v-bind:class="{'input-error': errors.has('name')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('name', 'required')">
                                            Este campo es requerido
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('price')}">
                                        <label for="name">Precio</label>
                                        <input
                                                type="number"
                                                class="form-control"
                                                id="price"
                                                name="price"
                                                placeholder="Precio"
                                                v-model="form.price"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:disabled="loading"
                                                v-bind:class="{'input-error': errors.has('price')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('price', 'required')">
                                            Este campo es requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="what_require">Requerido</label>
                                        <select
                                                name="what_require"
                                                id="what_require"
                                                v-model="form.what_require"
                                                class="form-control"
                                                v-bind:disabled="loading"
                                        >
                                            <option :value="0">Sin requerimiento</option>
                                            <option :value="1">Laboratorio</option>
                                            <option :value="2">Gasto</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <button class="btn btn-warning" v-bind:disabled="loading">
                                        <i class="glyphicon glyphicon-saved"></i>
                                        Actualizar producto
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
                                        Eliminar producto
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <h4>¿Esta seguro de eliminar este producto?</h4>
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
</template>

<script>
    export default {
        props: ['viewData', 'user'],

        data: function () {
            return {
                loading: false,
                form: JSON.parse(this.viewData),
                public_id: JSON.parse(this.viewData).public_id,
                unique: false,
                authUser: JSON.parse(this.user)
            }
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.validate();
                    }
                })
            },

            sendForm: function () {

                axios.put('/user/product/' + this.public_id, this.form)
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

                            console.log('Error', err);
                        })
                ;
            },

            validate: function () {

                this.loading = true;

                axios.get('/user/product/validate/' + this.form.public_id + '/' + this.form.id)
                    .then((res) => {

                        if (res.data.success && res.data.valid) {
                            this.sendForm();
                        } else {
                            this.unique = true;
                            this.loading = false;
                        }
                    })
                    .catch((err) => {
    if (err.response.status === 403 || err.response.status === 405) {
        location.href = '/';
    }
                        this.loading = false;

                        console.log('Error', err);
                    })
            },

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/product/' + this.form.public_id)
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
