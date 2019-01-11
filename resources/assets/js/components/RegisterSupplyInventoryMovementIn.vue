<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-log-in" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Entrada de insumos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th width="15%" class="text-center">Prestamo</th>
                                    <th width="25%">Insumo</th>
                                    <th width="15%">Marca</th>
                                    <th width="15%">Tipo</th>
                                    <th width="15%">Dimensi&oacute;n</th>
                                    <th width="10%">Qty</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(current, i) in data" :key="i">
                                    <td class="text-center">
                                        <label
                                            :class="{
                                                'text-success': current.isLoan,
                                                'text-danger': !current.isLoan
                                            }"
                                        >
                                            {{ current.isLoan ? 'SI' : 'NO' }}
                                        </label>
                                        <input
                                                type="checkbox"
                                                v-model="current.isLoan"
                                                @change="changeType(i)"
                                        >
                                    </td>
                                    <td>
                                        <div v-if="! current.isLoan">
                                            <!-- Insumo -->
                                            <select
                                                    :name="'supplyOrLoan' + i"
                                                    :id="'supplyOrLoan' + i"
                                                    class="form-control"
                                                    :class="{'input-error': errors.has('supplyOrLoan' + i)}"
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
                                            <p class="error" v-if="errors.firstByRule('supplyOrLoan' + i, 'required')">
                                                Requerido
                                            </p>
                                        </div>

                                        <div v-if="current.isLoan">
                                            <!-- Prestamo -->
                                            <select
                                                    :name="'supplyOrLoan' + i"
                                                    :id="'supplyOrLoan' + i"
                                                    class="form-control"
                                                    :class="{'input-error': errors.has('supplyOrLoan' + i)}"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    v-model="current.loan_id"
                                                    @change="changeLoan(i)"
                                            >
                                                <option
                                                        v-for="loan in loans"
                                                        :key="loan.id"
                                                        :value="loan.id"
                                                        v-show="! hasSelectedLoan(loan.id)"
                                                >
                                                    {{
                                                        loan.supply_inventory_movement_out.inventory_movement.user.name +
                                                        ' - ' +
                                                        loan.supply_inventory_movement_out.supply.name
                                                    }}
                                                </option>
                                            </select>
                                            <p class="error" v-if="errors.firstByRule('supplyOrLoan' + i, 'required')">
                                                Requerido
                                            </p>
                                        </div>
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
                                            :readonly="current.isLoan"
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
                                    <td colspan="6">
                                        <button
                                                class="btn btn-success"
                                                @click="addMovement"
                                                v-if="data.length < (supplies.length + loans.length)"
                                        >
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
            },
            loans: {
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
                    qty: null,
                    isLoan: false,
                    loan_id: null,
                    loan: null
                }]
            }
        },

        methods: {
            addMovement() {
                if (this.data.length < (this.supplies.length + this.loans.length)) {
                    this.data.push({
                        supply_id: null,
                        supply: null,
                        qty: null,
                        isLoan: false,
                        loan_id: null,
                        loan: null
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

            changeLoan(index) {
                const loanId = this.data[index].loan_id;

                this.loans.forEach(loan => {
                    if (loan.id === loanId) {
                        this.data[index].loan = loan;
                        this.data[index].supply = loan.supply_inventory_movement_out.supply;
                        this.data[index].supply_id = loan.supply_inventory_movement_out.supply.id;
                        this.data[index].qty = loan.supply_inventory_movement_out.qty * -1;
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

            hasSelectedLoan(loanId) {
                let hasSelected = false;

                this.data.forEach(movement => {
                    if (movement.loan_id === loanId) {
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
            },

            changeType(index) {
                this.data[index].supply_id = null;
                this.data[index].supply = null;
                this.data[index].loan_id = null;
                this.data[index].loan = null;
                this.data[index].qty = null;
            }
        }
    }
</script>
