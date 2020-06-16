<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-stats" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de Pagos
                </h1>
            </div>
        </div>

        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home">Pagos vs Gastos</a></li>
            <li><a data-toggle="tab" href="#menu1">Pagos vs Tipos de gastos</a></li>
        </ul>

        <div class="tab-content">
            <div id="home" class="tab-pane fade in active mt-2">
                <div class="row">
                    <div class="col-xs-4">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
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

                                    <div class="col-sm-12">
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
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-8">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <section>
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <line-chart-simple :data="datacollection"></line-chart-simple>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="menu1" class="tab-pane fade  mt-2">
                <div class="row">
                    <div class="col-xs-12 ">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3">
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

                                        <div class="col-sm-12  col-md-3">
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

                                        <div class="col-sm-12  col-md-3">
                                            <div class="form-group">
                                                <label for="type">Tipo</label>
                                                <select
                                                        name="type"
                                                        id="type"
                                                        class="form-control"
                                                        v-model="data.type"
                                                        >
                                                    <option value="0">Todos</option>
                                                    <option value="1">Tarjeta de credito</option>
                                                    <option value="2">Efectivo</option>
                                                    <option value="3">Cheque</option>
                                                    <option value="4">Descuento</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-xs-12  col-md-3 mt-2">
                                            <div class="form-group">
                                                <button
                                                        class="btn btn-primary"
                                                        @click="searchType()"
                                                        v-if="!loading"
                                                        >
                                                    <i class="glyphicon glyphicon-search"></i>
                                                    Buscar
                                                </button>
                                                <img src="/img/loading.gif" v-if="loading">
                                            </div>
                                        </div>

                                    </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <p>
                                                    <strong>Total de pagos: {{ data.total }}$</strong>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-xs-12">
                                            <line-chart :data="datacollectionForType"></line-chart>
                                        </div>
                                    </div>
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
    import LineChartSimple from './LineChartSimple';
    import LineChart from './LineChart';

    export default {
        components: {
            Datepicker,
            LineChart,
            LineChartSimple
        },
        data: function () {
            return {
              loading: false,
              initStart: new Date(),
              initEnd: new Date(),
              datacollection: [],
              datacollectionForType: [],
              data: {
                  start: '',
                  end: '',
                  type: [],
                  total: 0
              },
            }
        },
        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.start = year + '-' + month + '-' + day;
            this.data.end = year + '-' + month + '-' + day;
            this.search();
        },
        methods: {
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

            search: function () {
                this.loading = true;
                let payments = 0;
                let commission = 0;
                let expenses = 0;
                this.datacollection = [];
                axios.get(
                    `/admin/data/grafica/comicion_servicios/${this.data.start}/${this.data.end}`
                )
                .then((res) => {
                    this.loading = false;
                    this.datacollection.push({
                        totalPayments: res.data.totalPayments,
                        totalExpenses: res.data.totalExpenses,
                        titleExpenses: this.formatTitleExpenses(res.data)
                    });
                    console.log( this.datacollection)
                })
                .catch((err) => {
                    
                    console.log(err);
                    this.loading = false;
                    this.data.payments = [];
                })
            },

             searchType: function () {
                this.loading = true;
                this.datacollectionForType = [];
                axios.get(
                    `/admin/data/grafica/expenseForType/${this.data.start}/${this.data.end}/${this.data.type}`
                )
                .then((response) => {
                    this.loading = false;
                    this.data.total = response.data.totalPayments
                    response.data.totalExpenses.forEach((data) => {
                        this.datacollectionForType.push({
                            type: this.formatTitleExpensesForType(data),
                            amount: data.amount
                        });
                    })
                    
                })
                .catch((error) => {
                    
                    console.log(error);
                    this.loading = false;
                    this.data.payments = [];
                    
                })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },
            formatTitleExpenses: function (data) {
                let percentage = 0
                percentage = ( ( data.totalExpenses / data.totalPayments ) * 100 )
                let totalExpenses = `Gastos (${data.totalExpenses}$) ${parseFloat(percentage).toFixed(2)}%`
                return totalExpenses
            },

            formatTitleExpensesForType: function (data) {
                let percentage = 0
                percentage = ( ( data.amount / this.data.total ) * 100 )
                let totalExpenses = `${data.supplier.name} (${data.amount}$) ${parseFloat(percentage).toFixed(2)}%`
                return totalExpenses
            },
            
        }
    }
</script>

<style>
    ul > li .active{
        background-color: #0080FF!important;
        color:aliceblue!important;
    }

    .mt-2 {
        margin-top: 2em!important
    }
</style>