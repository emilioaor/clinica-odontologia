<template>
    <section>
        <table class="table table-responsive table-striped">
            <thead>
                <tr>
                    <th>C&oacute;digo</th>
                    <th>Insumo</th>
                    <th>Marca</th>
                    <th>Tipo</th>
                    <th width="5%"></th>
                    <td width="5%"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(supply, i) in supplies" :key="supply.id">
                    <td>{{ supply.public_id }}</td>
                    <td>
                        <span v-if="supplyEditing === i">

                            <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Insumo"
                                    :name="'supply' + i"
                                    v-model="supply.name"
                                    :class="{'input-error': errors.has('supply' + i)}"
                                    v-validate
                                    data-vv-rules="required"
                            >
                            <span class="error" v-if="errors.firstByRule('supply' + i, 'required')">
                                Requerido
                            </span>

                        </span>
                        <span v-else>
                            {{ supply.name }}
                        </span>
                    </td>
                    <td>
                        <span v-if="supplyEditing === i">

                            <select
                                    class="form-control"
                                    v-model="supply.supply_brand_id"
                            >
                                <option
                                        v-for="supplyBrand in supplyBrands"
                                        :key="supplyBrand.id"
                                        :value="supplyBrand.id"
                                >
                                    {{ supplyBrand.name }}
                                </option>
                            </select>

                        </span>
                        <span v-else>
                            {{ supply.supply_brand.name }}
                        </span>
                    </td>
                    <td>
                        <span v-if="supplyEditing === i">

                            <select
                                    class="form-control"
                                    v-model="supply.supply_type_id"
                            >
                                <option
                                        v-for="supplyType in supplyTypes"
                                        :key="supplyType.id"
                                        :value="supplyType.id"
                                >
                                    {{ supplyType.name }}
                                </option>
                            </select>

                        </span>
                        <span v-else>
                            {{ supply.supply_type.name }}
                        </span>
                    </td>
                    <td>
                        <!-- Guardar -->
                        <button
                                class="btn btn-success"
                                v-if="supplyEditing === i && supplyLoading !== i"
                                @click="validateSupply(i)"
                        >
                            <i class="glyphicon glyphicon-check"></i>
                        </button>
                    </td>
                    <td>
                        <!-- Editar -->
                        <button
                                class="btn btn-warning"
                                @click="supplyEditing = i"
                                v-if="supplyEditing !== i && supplyLoading !== i"
                                :disabled="supplyLoading !== null"
                        >
                            <i class="glyphicon glyphicon-edit"></i>
                        </button>

                        <!-- Cancelar -->
                        <button
                                type="button"
                                class="btn btn-warning"
                                @click="supplyEditing = null"
                                v-if="supplyEditing === i && supplyLoading !== i"
                        >
                            <i class="glyphicon glyphicon-remove-sign"></i>
                        </button>

                        <!-- Loading -->
                        <img src="/img/loading.gif" v-if="supplyLoading === i">
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</template>

<script>
    export default {
        props: {
            supplies: {
                type: Array,
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

        data() {
            return {
                supplyEditing: null,
                supplyLoading: null
            }
        },

        methods: {
            validateSupply(index) {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendSupply(index)
                    }
                })
            },

            sendSupply(index) {
                this.supplyLoading = index
                const supply = this.supplies[index]

                axios.put('/user/supply/list/' + supply.public_id, {...supply})
                    .then((res) => {
                        if (res.data.success) {
                            location.reload()
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.supplyLoading = null;

                        console.log('Error', err);
                    })
            }
        }
    }
</script>