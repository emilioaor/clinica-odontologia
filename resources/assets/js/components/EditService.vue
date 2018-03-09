<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-pencil" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Servicios prestados
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Cotización</label>
                                    <p>
                                        #{{ data.public_id }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Paciente</label>
                                    <p>
                                        {{ data.patient.name }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Telefono</label>
                                    <p>
                                        {{ data.patient.phone }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <p>
                                        {{ data.patient.email }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Monto</label>
                                    <p>
                                        ${{ data.total_footer_value }} USD
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xs-12">
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="50%">Servicio</th>
                                            <th width="40%">Diente</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(service, id) in services">
                                        <tr>
                                            <td class="text-center">{{ id + 1 }}</td>
                                            <td>
                                                <select
                                                        :name="'product' + id"
                                                        :id="'product' + id"
                                                        class="form-control"
                                                        v-model="service.product_id"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('product' + id)}"
                                                    >
                                                    <option
                                                            v-for="product in productList"
                                                            :value="product.id"
                                                            >
                                                        {{ product.name }}
                                                    </option>
                                                </select>
                                                <p class="error" v-if="errors.firstByRule('product' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        :name="'description' + id"
                                                        :id="'description' + id"
                                                        v-model="service.description"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('description' + id)}"
                                                    >
                                                <p class="error" v-if="errors.firstByRule('description' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <a @click="removeService(id)">
                                                    X
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button class="btn btn-success" @click="addService()">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Agregar servicio
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <button class="btn btn-primary btn-lg" @click="validateForm()">
                                    <i class="glyphicon glyphicon-check"></i>
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['budget', 'products'],

        data: function () {
            return {
                loading: false,
                data: {},
                productList: [],
                services: []
            }
        },
        beforeMount: function () {
            this.data = JSON.parse(this.budget);
            this.productList = JSON.parse(this.products);
            this.services = this.data.services;
        },

        methods: {
            addService: function () {
                this.services.push({
                    description: '',
                    product_id: null
                });
            },

            removeService: function (index) {
                this.services.splice(index, 1);
            },

            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                axios.put('/user/service/' + this.data.public_id, {
                    services: this.services
                })
                    .then((res) => {
                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                    })
            }
        }
    }
</script>