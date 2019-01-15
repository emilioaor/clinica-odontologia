<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de movimientos de insumos
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
                                    <label for="">Desde</label>
                                    <datepicker
                                            name = "start"
                                            id = "start"
                                            language="es"
                                            input-class = "form-control"
                                            format = "MM/dd/yyyy"
                                            @input="changeStart($event)"
                                            v-model="initStart"
                                    ></datepicker>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Hasta</label>
                                    <datepicker
                                            name = "end"
                                            id = "end"
                                            language="es"
                                            input-class = "form-control"
                                            format = "MM/dd/yyyy"
                                            @input="changeEnd($event)"
                                            v-model="initEnd"
                                    ></datepicker>
                                </div>
                            </div>

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
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="type">Tipo</label>
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
                                    <label for="movement_type">Tipo de movimiento</label>
                                    <select
                                            name="movement_type"
                                            id="movement_type"
                                            class="form-control"
                                            v-model="data.movement_type"
                                    >
                                        <option :value="0">- Todos</option>
                                        <option :value="1">Entrada</option>
                                        <option :value="2">Salida</option>
                                    </select>
                                </div>
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
                                            <th>Fecha</th>
                                            <th>Descripci&oacute;n</th>
                                            <th>Insumo</th>
                                            <th>Marca</th>
                                            <th>Tipo</th>
                                            <th width="20%" class="text-center">Qty</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(inventory, i) in data.results" :key="i">
                                            <td>{{ inventory.date }}</td>
                                            <td>{{ inventory.description }}</td>
                                            <td>{{ inventory.supply.name }}</td>
                                            <td>{{ inventory.supply.supply_brand.name }}</td>
                                            <td>{{ inventory.supply.supply_type.name }}</td>
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

    export default {
        components: {
            Datepicker
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
                initStart: new Date(),
                initEnd: new Date(),
                data: {
                    brand: 0,
                    type: 0,
                    start: null,
                    end: null,
                    movement_type: 0,
                    results: []
                },
           }
        },

        mounted() {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.start = year + '-' + month + '-' + day;
            this.data.end = year + '-' + month + '-' + day;
        },
       
       methods: {
        
            search: function () {
                this.loading = true;

                axios.get(
                        '/admin/report/inventorySupplyMovementData' +
                        '?type=' + this.data.type +
                        '&brand=' + this.data.brand +
                        '&start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&movement_type=' + this.data.movement_type
                )
                    .then((res) => {
                        this.loading = false;

                        this.data.results = [];

                        if (res.data.success) {
                            this.data.results = res.data.inventory                           
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.results = [];
                    })
            },

           changeStart: function (date) {
               let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
               let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
               let year = date.getFullYear();

               this.data.start = year + '-' + month + '-' + day;
           },

           changeEnd: function (date) {
               let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
               let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
               let year = date.getFullYear();

               this.data.end = year + '-' + month + '-' + day;
           },
       }
    }
</script>
