<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-file" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Reporte de pacientes y pacientes con servicios
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
                                        <label for="start">Desde</label>
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
                                        <label for="end">Hasta</label>
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
                                        <label for="reference">Referencia</label>
                                        <select
                                                name="reference"
                                                id="reference"
                                                class="form-control"
                                                v-model="data.reference"
                                                >
                                            <option value="0">Todas</option>
                                            <option
                                                    v-for="reference in references"
                                                    :value="reference.id"
                                                    >
                                                {{ reference.description }}
                                            </option>
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
                                    <p>
                                        <strong>Pacientes registrados:</strong>
                                        {{ data.patients }}
                                    </p>
                                </div>

                                <div class="col-xs-12">
                                    <p>
                                        <strong>Pacientes que se les hizo un servicio:</strong>
                                        {{ data.patientsWithServices }}
                                    </p>
                                </div>

                                <div class="col-xs-12">
                                    <p>
                                        <strong>Porcentaje de conversi&oacute;n:</strong>
                                        {{ conversion }}%
                                    </p>
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
        props: {
            references: {
                type: Array,
                required: true
            }
        },
        computed: {
            conversion: function () {

                if (this.data.patientsWithServices === null || this.data.patients === null) {
                    return null;
                }

                if (this.data.patients === 0) {
                    return 0;
                }

                const conversion = (this.data.patientsWithServices * 100) / this.data.patients;

                return conversion.toFixed(2);
            }
        },
        data: function () {
            return {
              loading: false,
              initStart: new Date(),
              initEnd: new Date(),
              data: {
                  start: '',
                  end: '',
                  reference: 0,
                  patients: null,
                  patientsWithServices: null
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
                        '/admin/report/patientsAndPatientsWithServicesData' +
                        '?start=' + this.data.start +
                        '&end=' + this.data.end +
                        '&reference=' + this.data.reference
                )
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.patients = res.data.patients;
                            this.data.patientsWithServices = res.data.patientsWithServices;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.patients = null;
                        this.data.patientsWithServices = null;
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            }
        }
    }
</script>