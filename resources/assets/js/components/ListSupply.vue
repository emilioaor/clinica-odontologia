<template>
    <section>
        <div class="row">
            <div class="col-sm-3">
                <select
                        id="supply_brand"
                        class="form-control"
                        v-model="filter.brand"
                >
                    <option :value="0">- Marca -</option>
                    <option
                            v-for="supplyBrand in supplyBrands"
                            :key="supplyBrand.id"
                            :value="supplyBrand.id"
                    >
                        {{ supplyBrand.name }}
                    </option>
                </select>
            </div>

            <div class="col-sm-3">
                <select
                        id="supply_type"
                        class="form-control"
                        v-model="filter.type"
                >
                    <option :value="0">- Tipo -</option>
                    <option
                            v-for="supplyType in supplyTypes"
                            :key="supplyType.id"
                            :value="supplyType.id"
                    >
                        {{ supplyType.name }}
                    </option>
                </select>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                    <div class="d-flex">
                        <div class="w-40">
                            <input
                                    type="text"
                                    class="form-control"
                                    v-model="filter.width"
                                    v-money="mask"
                            >
                        </div>
                        <div class="w-20 text-center">X</div>
                        <div class="w-40">
                            <input
                                    type="text"
                                    class="form-control"
                                    v-model="filter.height"
                                    v-money="mask"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-2">
                <button class="btn btn-primary" @click="filterApply()">
                    <i class="glyphicon glyphicon-filter"></i>
                </button>

                <button
                        class="btn btn-danger"
                        @click="resetFilter()"
                        v-if="lastFilter.brand > 0 || lastFilter.type > 0 || lastFilter.width !== '0,00' || lastFilter.height !== '0,00'"
                >
                    <i class="glyphicon glyphicon-remove"></i>
                </button>

            </div>
        </div>
        <br>
        <table class="table table-responsive table-striped">
            <thead>
                <tr>
                    <th>C&oacute;digo</th>
                    <th>Insumo</th>
                    <th>Marca</th>
                    <th>Tipo</th>
                    <th>Dimensi&oacute;n</th>
                    <th width="5%"></th>
                    <td width="5%"></td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(supply, i) in supplies" :key="supply.id">
                    <td>
                        <a :href="'/user/supply/' + supply.public_id + '/edit'">
                            {{ supply.public_id }}
                        </a>
                    </td>
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
                        {{ supply.width + 'x' + supply.height }}
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
    import {VMoney} from 'v-money';

    export default {
        directives: {
            VMoney
        },
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
            },
            lastFilter: {
                type: Object,
                required: true
            }
        },

        data() {
            return {
                supplyEditing: null,
                supplyLoading: null,
                filter: {
                    brand: 0,
                    type: 0,
                    width: 0,
                    height: 0
                },
                mask: {
                    decimal: ',',
                    thousands: '.',
                    prefix: '',
                    suffix: '',
                    precision: 2
                }
            }
        },

        mounted() {
            this.initFilter();
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
            },

            filterApply() {

                let separator = '?';
                let url = location.origin + '/user/supply';

                if (this.filter.brand > 0) {
                    url = url + separator + 'brand=' + this.filter.brand;
                    separator = '&';
                }

                if (this.filter.type > 0) {
                    url = url + separator + 'type=' + this.filter.type;
                    separator = '&';
                }

                if (this.filter.width !== '0,00') {
                    url = url + separator + 'width=' + this.filter.width;
                    separator = '&';
                }

                if (this.filter.height !== '0,00') {
                    url = url + separator + 'height=' + this.filter.height;
                    separator = '&';
                }

                location.href = url;
            },

            resetFilter() {
                location.href = location.origin + '/user/supply';
            },

            initFilter() {
                this.filter.brand = this.lastFilter.brand ? this.lastFilter.brand : 0;
                this.filter.type = this.lastFilter.type ? this.lastFilter.type : 0;
                this.filter.width = this.lastFilter.width ? this.lastFilter.width : 0;
                this.filter.height = this.lastFilter.height ? this.lastFilter.height : 0;
            }
        }
    }
</script>

<style>
    .d-flex {
        display: flex;
    }
    .w-40 {
        min-width: 40%;
    }
    .w-20 {
        min-width: 20%;
    }
</style>