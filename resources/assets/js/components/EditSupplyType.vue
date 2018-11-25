<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Tipos de insumos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">
                        
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(supplyType, i) in data"
                                    :key="supplyType.id"
                                >
                                    <td>{{ supplyType.public_id }}</td>
                                    <td>
                                        <input 
                                            type="text"
                                            class="form-control" 
                                            :name="'name' + i"
                                            v-model="supplyType.name"
                                            v-validate
                                            data-vv-rules="required"
                                            maxlength="50"
                                            :class="{'input-error': errors.firstByRule('name' + i, 'required')}"
                                            >
                                        <p class="error" v-if="errors.firstByRule('name' + i, 'required')">
                                            Requerido
                                        </p>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger" @click="removeType(i)">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3">
                                        <button class="btn btn-success" @click="addType">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Agregar tipo
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="form-group">
                            <button class="btn btn-primary" v-if="! loading" @click="validateForm">
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
</template>

<script>
    export default {
        props: {
            supplyTypes: {
                type: Array,
                required: true
            }
        },

        data () {
            return {
                loading: false,
                data: this.supplyTypes
            }
        },

        methods: {

            addType() {
                this.data.push({
                    name: null,
                    public_id: null
                });
            },

            removeType(index) {
                this.data.splice(index, 1);
            },

            validateForm() {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm() {
                this.loading = true;

                axios.put('/user/supplyType', {supplyTypes: this.data})
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
            }
        }
    }
</script>

