<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Entrada de insumos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th width="31%">Insumo</th>
                                    <th width="22%">Marca</th>
                                    <th width="22%">Tipo</th>
                                    <th width="20%">Qty</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(current, i) in data" :key="i">
                                    <td>
                                        <select 
                                            :name="'supply' + i" 
                                            :id="'supply' + i" 
                                            class="form-control"
                                            :class="{'input-error': errors.has('supply' + i)}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="current.supply_id"
                                            @change="changeSupply(i)"
                                            >
                                            <option 
                                                v-for="supply in supplies"
                                                :key="supply.id"
                                                :value="supply.id"
                                                v-show="! hasSelected(supply.id)"
                                                >
                                                {{ supply.name }}
                                            </option>
                                        </select>
                                        <p class="error" v-if="errors.firstByRule('supply' + i, 'required')">
                                            Requerido
                                        </p>
                                    </td>
                                    <td>
                                        {{ current.supply ? current.supply.supply_brand.name : '-' }}
                                    </td>
                                    <td>
                                        {{ current.supply ? current.supply.supply_type.name : '-' }}
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            :name="'qty' + i" 
                                            :id="'qty' + i" 
                                            class="form-control"
                                            :class="{'input-error': errors.has('qty' + i)}"
                                            v-validate
                                            data-vv-rules="required"
                                            v-model="current.qty"
                                            >
                                        <p class="error" v-if="errors.firstByRule('qty' + i, 'required')">
                                            Requerido
                                        </p>
                                    </td>
                                    <td>
                                        <button class="btn btn-danger" @click="removeMovement(i)">
                                            <i class="glyphicon glyphicon-remove"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <button class="btn btn-success" @click="addMovement" v-if="data.length < supplies.length">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Agregar
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
            supplies: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                loading: false,
                data: [{
                    supply_id: null,
                    supply: null,
                    qty: null
                }]
            }
        },

        methods: {
            addMovement() {
                if (this.data.length < this.supplies.length) {
                    this.data.push({
                        supply_id: null,
                        supply: null,
                        qty: null
                    });
                }
            },

            removeMovement(index) {
                if (this.data.length > 1) {
                    this.data.splice(index, 1);
                }
            },

            changeSupply(index) {
                const supplyId = this.data[index].supply_id;

                this.supplies.forEach(supply => {
                    if (supply.id === supplyId) {
                        this.data[index].supply = supply;
                    }
                });
            },

            hasSelected(supplyId) {
                let hasSelected = false;

                this.data.forEach(movement => {
                    if (movement.supply_id === supplyId) {
                        hasSelected = true;
                    }
                });

                return hasSelected;
            },

            validateForm() {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                })
            },

            sendForm() {
                this.loading = true;

                axios.post('/user/supplyInventoryMovement/in', {inventoryMovements: this.data})
                    .then((res) => {
                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }

                        this.loading = false;
                    })
                ;
            }
        }
    }
</script>
