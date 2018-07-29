<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-copy" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Solicitar insumo
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form v-on:submit.prevent="validateForm()">

                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Libre</th>
                                                <th>Insumo</th>
                                                <th>Cantidad</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(detail, id) in form.details">
                                                <th width="10%" class="text-center">
                                                    <input type="checkbox" v-model="detail.freeText">
                                                </th>
                                                <td width="70%" v-if="! detail.freeText">
                                                    <select
                                                            :name="'supply' + id"
                                                            :id="'supply' + id"
                                                            class="form-control"
                                                            v-model="detail.supply"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('supply' + id)}"
                                                        >
                                                        <option
                                                                v-for="supply in supplies"
                                                                :value="supply.id"
                                                            >
                                                            {{ supply.name }}
                                                        </option>
                                                    </select>
                                                    <p class="error" v-if="errors.firstByRule('supply' + id, 'required')">
                                                        Campo requerido
                                                    </p>
                                                </td>
                                                <td width="70%" v-if="detail.freeText">
                                                    <input
                                                            type="text"
                                                            :name="'description' + id"
                                                            :id="'description' + id"
                                                            class="form-control"
                                                            v-model="detail.description"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('description' + id)}"
                                                            >
                                                    <p class="error" v-if="errors.firstByRule('description' + id, 'required')">
                                                        Campo requerido
                                                    </p>
                                                </td>
                                                <td width="20%">
                                                    <input
                                                            type="number"
                                                            :name="'qty' + id"
                                                            :id="'qty' + id"
                                                            class="form-control"
                                                            v-model="detail.qty"
                                                            v-validate
                                                            data-vv-rules="required|min_value:1"
                                                        >
                                                    <p class="error" v-if="errors.firstByRule('qty' + id, 'required')">
                                                        Campo requerido
                                                    </p>
                                                    <p class="error" v-if="errors.firstByRule('qty' + id, 'min_value')">
                                                        Campo requerido
                                                    </p>
                                                </td>
                                                <td>
                                                    <a @click="removeDetail(id)">
                                                        X
                                                    </a>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">
                                                    <button type="button" class="btn btn-success" @click="addDetail()">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        Agregar
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <img src="/img/loading.gif" v-if="loading">
                                    <button class="btn btn-primary btn-lg" v-if="!loading">
                                        <i class="glyphicon glyphicon-copy"></i>
                                        Solicitar
                                    </button>
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
        props: ['suppliesData'],
        data: function () {
            return {
                loading: false,
                supplies: JSON.parse(this.suppliesData),
                form: {
                    details: [{
                        supply: null,
                        qty: 1,
                        freeText: false,
                        description: null
                    }]
                }
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

                axios.post('/user/supplyRequest', this.form)
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

            addDetail: function () {
                this.form.details.push({
                    supply: null,
                    qty: 1,
                    freeText: false,
                    description: null
                });
            },

            removeDetail: function (index) {
                if (this.form.details.length > 1) {
                    this.form.details.splice(index, 1);
                }
            }
        }
    }
</script>
