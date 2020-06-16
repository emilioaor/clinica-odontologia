<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Stock de insumos
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
                                    <label for="brand">Marca</label>
                                    <select 
                                        name="brand" 
                                        id="brand"
                                        class="form-control"
                                        v-model="data.brand"
                                        >
                                        <option :value="0">- Todos</option>
                                        <option 
                                            v-for="supplyBrand in supplyBrands"
                                            :key="supplyBrand.id"
                                            :value="supplyBrand.id"
                                            >
                                            {{ supplyBrand.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="brand">Tipo</label>
                                    <select 
                                        name="type" 
                                        id="type"
                                        class="form-control"
                                        v-model="data.type"
                                        >
                                        <option :value="0">- Todos</option>
                                        <option 
                                            v-for="supplyType in supplyTypes"
                                            :key="supplyType.id"
                                            :value="supplyType.id"
                                            >
                                            {{ supplyType.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Dimensi&oacute;n</label>
                                    <div class="d-flex">
                                        <div class="w-40">
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="data.width"
                                                v-money="mask"
                                            >
                                        </div>
                                        <div class="w-20 text-center">X</div>
                                        <div class="w-40">
                                            <input
                                                type="text"
                                                class="form-control"
                                                v-model="data.height"
                                                v-money="mask"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Cantidad</label>
                                    <select 
                                        name="type" 
                                        id="type"
                                        class="form-control"
                                        v-model="data.quantity"
                                        >
                                        <option :value="0">- Todas</option>
                                        <option :value="2">2</option>
                                        <option :value="3">3</option>
                                        <option :value="4">4</option>
                                        <option :value="5">5</option>
                                        <option :value="6">6</option>
                                        <option :value="7">7</option>
                                        <option :value="8">8</option>
                                        <option :value="9">9</option>
                                        <option :value="10">10</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <label for="">Filtrar por Insumo, Marca, Tipo</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Buscador"
                                    v-model="searchData"
                                    @keyup="searchSupply()"
                                >
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <button
                                            class="btn btn-primary"
                                            @click="search()"
                                            v-if="!loading"
                                            >
                                        <i class="glyphicon glyphicon-search"></i>
                                        Buscar
                                    </button>

                                    <img src="/img/loading.gif" v-if="loading">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Insumo</th>
                                            <th>Marca</th>
                                            <th>Tipo</th>
                                            <th>Dimensi&oacute;n</th>
                                            <th width="20%" class="text-center">Disponible</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(inventory, i) in data.results" :key="i">
                                            <td>{{ inventory.supply.nameSupply }}</td>
                                            <td>{{ inventory.supply.nameBrands }}</td>
                                            <td>{{ inventory.supply.nameType }}</td>
                                            <td>{{ inventory.supply.width + 'x' + inventory.supply.height }}</td>
                                            <td class="text-center">{{ inventory.qty }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import {VMoney} from 'v-money';

    export default {
        components: {
            Datepicker
        },
        directives: {
            VMoney
        },
        props: {
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
                loading: false,
                searchData: '',
                data: {
                    brand: 0,
                    type: 0,
                    quantity: 0,
                    width: '',
                    height: '',
                    dataSupply: '',
                    results: []
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

       methods: {
            searchSupply: function () {
                this.loading = true;
                this.data.dataSupply = this.searchData
                this.search()
            },
            search: function () {
                this.loading = true;

                axios.post('/user/report/stockData' ,this.data)
                    .then((res) => {
                        this.loading = false;
                        
                        this.data.results = [];

                        if (res.data.success) {
                            this.data.results = res.data.inventory                           
                        }
                        console.log(res)
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err.response.data);
                        this.loading = false;
                        this.data.results = [];
                    })
            },
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
