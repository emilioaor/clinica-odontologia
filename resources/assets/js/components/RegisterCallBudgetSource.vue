<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Fuentes de presupuesto
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-responsive">
                                    <thead>
                                    <tr>
                                        <th>Fuente</th>
                                        <th width="5%"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(reference, i) in form.references" :key="i">
                                        <td>
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Fuente"
                                                    :name="'reference' + i"
                                                    :id="'reference' + i"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    v-model="reference.name"
                                                    :class="{'input-error': errors.has('reference' + i)}"
                                                    >

                                            <p class="error" v-if="errors.firstByRule('reference' + i, 'required')">
                                                Requerido
                                            </p>
                                        </td>
                                        <td>
                                            <button class="btn btn-danger" @click="removeReference(i)">
                                                <i class="glyphicon glyphicon-remove"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td colspan="2">
                                            <button class="btn btn-success" @click="addReference()">
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
                                <button class="btn btn-primary" v-if="! loading" @click="validateForm()">
                                    <i class="glyphicon glyphicon-saved"></i>
                                    Guardar
                                </button>

                                <img src="/img/loading.gif" v-if="loading">
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
        props: {
            callBudgetSources: {
                type: Array,
                required: true
            }
        },
        data: function () {
            return {
                loading: false,
                form: {
                    references: []
                }
            }
        },

        mounted: function () {
            this.form.references = this.callBudgetSources;
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

                axios.post('/user/callBudgetSource', this.form)
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

            addReference: function () {
                this.form.references.push({
                    description: null
                });
            },

            removeReference: function (i) {
                this.form.references.splice(i, 1);
            }
        }
    }
</script>
