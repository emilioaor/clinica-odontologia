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
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form v-on:submit.prevent="validateForm()">

                            <div class="row">
                                <div class="col-sm-4">
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

                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('brand')}">
                                        <label for="brand">Marca</label>
                                        <select 
                                            name="brand" 
                                            id="brand"
                                            class="form-control"
                                            :class="{'input-error': errors.has('brand')}"
                                            :disabled="loading"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.supply_brand_id"
                                            >
                                                <option 
                                                    v-for="brand in supplyBrands"
                                                    :key="brand.id"
                                                    :value="brand.id">
                                                    {{ brand.name }}
                                                </option>
                                        </select>
                                        <p class="error" v-if="errors.firstByRule('brand', 'required')">
                                            Requerido
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('type')}">
                                        <label for="type">Tipo</label>
                                        <select 
                                            name="type" 
                                            id="type"
                                            class="form-control"
                                            :class="{'input-error': errors.has('type')}"
                                            :disabled="loading"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="form.supply_type_id"
                                            >
                                                <option 
                                                    v-for="type in supplyTypes"
                                                    :key="type.id"
                                                    :value="type.id">
                                                    {{ type.name }}
                                                </option>
                                        </select>
                                        <p class="error" v-if="errors.firstByRule('type', 'required')">
                                            Requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('height')}">
                                        <label for="height">Alto</label>
                                        <input
                                                type="number"
                                                class="form-control"
                                                id="height"
                                                name="height"
                                                placeholder="Alto"
                                                v-model="form.height"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:disabled="loading"
                                                v-bind:class="{'input-error': errors.has('height')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('height', 'required')">
                                            Requerido
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('width')}">
                                        <label for="width">Ancho</label>
                                        <input
                                                type="number"
                                                class="form-control"
                                                id="width"
                                                name="width"
                                                placeholder="Ancho"
                                                v-model="form.width"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:disabled="loading"
                                                v-bind:class="{'input-error': errors.has('width')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('width', 'required')">
                                            Requerido
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
        props: {
            viewData: {
                type: Object,
                required: true
            },
            user: {
                type: Object,
                required: true
            },
            supplyBrands: {
                type: Array,
                required: true
            },
            supplyTypes: {
                type: Array,
                required: true
            }
        },

        data: function () {
            return {
                loading: false,
                form: this.viewData,
                authUser: this.user
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
                            if (err.response.status === 403 || err.response.status === 405) {
                                location.href = '/';
                            }
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
