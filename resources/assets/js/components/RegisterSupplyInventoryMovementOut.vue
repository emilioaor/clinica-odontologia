<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-log-out" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Salida de insumos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="user">Entregado a</label>
                                    <select 
                                        name="user" 
                                        id="user"
                                        class="form-control"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('user')}"
                                        v-model="data.user_id"
                                        >
                                        <option
                                            v-for="user in users"
                                            :key="user.id"
                                            :value="user.id"
                                            >
                                            {{ user.name }}
                                        </option>
                                    </select>

                                    <p class="error" v-if="errors.firstByRule('user', 'required')">
                                        Requerido
                                    </p>
                                </div>
                            </div>
                        </div>

                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th width="25%">Insumo</th>
                                    <th width="15%">Marca</th>
                                    <th width="15%">Tipo</th>
                                    <th width="15%">Dimensi&oacute;n</th>
                                    <th width="10%">Qty</th>
                                    <th width="15%" class="text-center">Prestamo</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(current, i) in data.movements" :key="i">
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
                                        {{ current.supply ? current.supply.width + 'x' + current.supply.height : '-' }}
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
                                    <td class="text-center">
                                        <label
                                                v-if="current.supply"
                                                :class="{
                                                    'text-success': current.supply.loan_default,
                                                    'text-danger': !current.supply.loan_default
                                                }"
                                        >
                                            {{ current.supply.loan_default ? 'SI' : 'NO' }}
                                        </label>

                                        <input
                                                type="checkbox"
                                                v-model="current.supply.loan_default"
                                                v-if="current.supply"
                                        >
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
                                    <td colspan="6">
                                        <button class="btn btn-success" @click="addMovement" v-if="data.movements.length < supplies.length">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Agregar
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <p>
                                    <strong>Firmar como recibido:</strong>
                                </p>

                                <div class="form-group">
                                    <input
                                            type="text"
                                            name="mark_confirmation"
                                            id="mark_confirmation"
                                            class="form-control sing-input"
                                            :class="{'input-error': errors.has('mark_confirmation')}"
                                            v-model="data.mark_confirmation"
                                            v-validate
                                            data-vv-rules="required"
                                            v-show="! data.mark_confirmation"
                                            @click="showSignForm = true"
                                    >
                                    <p class="error" v-if="errors.firstByRule('mark_confirmation', 'required')">
                                        Requerido
                                    </p>

                                    <img
                                            class="sign-image"
                                            :src="data.mark_confirmation"
                                            alt="Firma"
                                            v-if="data.mark_confirmation"
                                            @click="showSignForm = true"
                                    >
                                </div>
                            </div>
                        </div>

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

        <div class="space-mark-confirmation" v-if="showSignForm">
            <draw-confirmation
                    @save-sign="saveSign"
                    @close="showSignForm = false"
            ></draw-confirmation>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            supplies: {
                type: Array,
                required: true
            },
            users: {
                type: Array,
                required: true
            }
        },

        data() {
            return {
                loading: false,
                data: {
                    user_id: null,
                    mark_confirmation: null,
                    movements: [{
                        supply_id: null,
                        supply: null,
                        qty: null
                    }]
                },
                showSignForm: false,
            }
        },

        methods: {
            addMovement() {
                if (this.data.movements.length < this.supplies.length) {
                    this.data.movements.push({
                        supply_id: null,
                        supply: null,
                        qty: null
                    });
                }
            },

            removeMovement(index) {
                if (this.data.movements.length > 1) {
                    this.data.movements.splice(index, 1);
                }
            },

            changeSupply(index) {
                const supplyId = this.data.movements[index].supply_id;

                this.supplies.forEach(supply => {
                    if (supply.id === supplyId) {
                        this.data.movements[index].supply = supply;
                    }
                });
            },

            hasSelected(supplyId) {
                let hasSelected = false;

                this.data.movements.forEach(movement => {
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

                axios.post('/user/supplyInventoryMovement/out', this.data)
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
            },

            saveSign(data) {
                this.showSignForm = false;
                this.data.mark_confirmation = data.base64;
            }
        }
    }
</script>

<style>
    .space-mark-confirmation {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,.7);
        z-index: 999999;
    }
    .sing-input {
        max-width: 20%;
        display: inline-block;
    }
    .sign-image {
        cursor: pointer;
        width: 100%;
        max-width: 200px;
    }
</style>
