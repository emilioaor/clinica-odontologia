<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    {{ form.public_id }}
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form v-on:submit.prevent="validateForm()">

                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('name')}">
                                        <label for="name">Nombre del insumo</label>
                                        <input
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                placeholder="Nombre del insumo"
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
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <button class="btn btn-warning" v-bind:disabled="loading">
                                        <i class="glyphicon glyphicon-saved"></i>
                                        Actualizar insumo
                                    </button>

                                    <button
                                            type="button"
                                            class="btn btn-danger"
                                            data-toggle="modal"
                                            data-target="#deleteModal"
                                            v-bind:disabled="loading"
                                            v-if="authUser.level === 1"
                                            >
                                        <i class="glyphicon glyphicon-remove"></i>
                                        Eliminar insumo
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <h4>¿Esta seguro de eliminar este insumo?</h4>
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
                authUser: JSON.parse(this.user)
            }
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                })
            },

            sendForm: function () {

                this.loading = true;

                axios.put('/user/supply/' + this.form.public_id, this.form)
                        .then((res) => {
                            if (res.data.success) {
                                location.href = res.data.redirect;
                            }
                        })
                        .catch((err) => {
                            this.loading = false;

                            console.log('Error', err);
                        })
                ;
            },

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/supply/' + this.form.public_id)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        this.loading = false;
                        console.log(err);
                    })
            }
        }
    }
</script>
