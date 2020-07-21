<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de accesos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <section>

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

                                    <!-- LoginHistory -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Usuario</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>IP</th>
                                                <th class="text-center">Usuario existe</th>
                                                <th class="text-center">Login exitoso</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr :key="i" v-for="(history, i) in data.histories">
                                                <td>{{ history.username }}</td>
                                                <td>{{ dateFormat(history.date) }}</td>
                                                <td>{{ timeFormat(history.date) }}</td>
                                                <td>{{ history.ip }}</td>
                                                <td class="text-center">
                                                    <span class="label label-success" v-if="history.user_id">
                                                        SI
                                                    </span>
                                                    <span class="label label-danger" v-else>
                                                        NO
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="label label-success" v-if="history.success">
                                                        SI
                                                    </span>
                                                    <span class="label label-danger" v-else>
                                                        NO
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

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

    export default {
        components: {
            Datepicker
        },

        data: function () {
            return {
                loading: false,
                initStart: new Date(),
                initEnd: new Date(),
                data: {
                  start: '',
                  end: '',
                  histories: []
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

                axios.get(
                        '/admin/report/loginHistoryData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end
                )
                    .then((res) => {
                        this.loading = false;
                        this.data.histories = [];

                        if (res.data.success) {
                            this.data.histories = res.data.histories;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.histories = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            timeFormat: function (date) {
                let format = date.split(' ');
                format = format[1].split(':');

                return format[0] + ':' + format[1];
            }
        }
    }
</script>