<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-stats" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de pagos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <section>
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

                                <div class="col-sm-12">
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
                        </section>
                    </div>
                </div>
            </div>
            <div class="col-xs-9">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <section>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <strong>Total: {{ data.total }}</strong>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <line-chart :data="datacollection"></line-chart>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>

    </section>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';
    import LineChart from './LineChart';

    export default {
        components: {
            Datepicker,
            LineChart
        },
        data: function () {
            return {
              loading: false,
              initStart: new Date(),
              initEnd: new Date(),
              datacollection: [],
              data: {
                  start: '',
                  end: '',
                  type: 0,
                  payments: [],
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
                let type = [];
                let amount = [];
                this.datacollection = [];
                axios.get(
                    `/admin/datos/grafica/pagos/${this.data.start}/${this.data.end}/${this.data.type}`
                )
                .then((res) => {
                    this.loading = false;
                    this.data.total = res.data.totalPayments.total;
                    res.data.totalAmounForType.forEach((data) => {
                        this.datacollection.push({
                            type: this.formatBarTitle(data),
                            amount: data.amount
                        });
                    })
                })
                .catch((err) => {
                    
                    console.log(err);
                    this.loading = false;
                    this.data.payments = [];
                })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },
            formatBarTitle: function (data) {
                let percentage = 0
                switch (data.type) {
                    case 1:
                        percentage = ( ( data.amount / this.data.total ) * 100 )
                        return `Tarjeta de credito (${data.amount}$) ${parseFloat(percentage).toFixed(2)}%`
                        break;
                    case 2:
                        percentage = ( ( data.amount / this.data.total ) * 100 )
                        return `Efectivo (${data.amount}$) ${parseFloat(percentage).toFixed(2)}%`
                        break;
                    case 3:
                        percentage = ( ( data.amount / this.data.total ) * 100 )
                        return `Cheque (${data.amount}$) ${parseFloat(percentage).toFixed(2)}%`
                        break;
                    case 4:
                        percentage = ( ( data.amount / this.data.total ) * 100 ) 
                        return `Descuento (${data.amount}$) ${parseFloat(percentage).toFixed(2)}%`
                        break;
                    default:
                        break;
                }
            }
        }
    }
</script>