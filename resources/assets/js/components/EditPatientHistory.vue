<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-pencil" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Servicios prestados
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">ID</label>
                                    <p>
                                        {{ data.public_id }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Paciente</label>
                                    <p>
                                        {{ data.name }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Telefono</label>
                                    <p>
                                        {{ data.phone }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <p>
                                        {{ data.email }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Fecha</label>
                                    <datepicker
                                            name = "date"
                                            id = "date"
                                            language="es"
                                            input-class = "form-control"
                                            format = "MM/dd/yyyy"
                                            v-model="date"
                                            @input="changeDate($event)"
                                            ></datepicker>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xs-12">
                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="5%" class="text-center">#</th>
                                            <th width="70%">Servicio</th>
                                            <th width="20%">Diente</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(service, id) in services">
                                        <tr>
                                            <td class="text-center">{{ id + 1 }}</td>
                                            <td>
                                                <select
                                                        :name="'product' + id"
                                                        :id="'product' + id"
                                                        class="form-control"
                                                        v-model="service.product_id"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('product' + id)}"
                                                    >
                                                    <option
                                                            v-for="product in productList"
                                                            :value="product.id"
                                                            >
                                                        {{ product.name }}
                                                    </option>
                                                </select>
                                                <p class="error" v-if="errors.firstByRule('product' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'tooth' + id"
                                                        :id="'tooth' + id"
                                                        class="form-control"
                                                        v-model="service.tooth"
                                                    >
                                                    <option
                                                            v-for="tooth in range(1, 32)"
                                                            :value="tooth"
                                                            >
                                                        {{ tooth }}
                                                    </option>
                                                </select>
                                                <p class="error" v-if="errors.firstByRule('tooth' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <a @click="removeService(id)">
                                                    X
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">
                                                <button class="btn btn-success" @click="addService()">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Agregar
                                                </button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <img src="/img/loading.gif" v-if="loading">
                                <button class="btn btn-primary btn-lg" @click="validateForm()" v-if="!loading">
                                    <i class="glyphicon glyphicon-check"></i>
                                    Guardar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        props: ['patient', 'products', 'historyDate'],
        components: {
            Datepicker
        },

        data: function () {
            return {
                loading: false,
                data: {},
                productList: [],
                services: [],
                date: ''
            }
        },
        beforeMount: function () {
            this.data = JSON.parse(this.patient);
            this.productList = JSON.parse(this.products);
            this.services = this.data.patient_history;

            const date = this.historyDate.split('-');

            this.setDate(new Date(date[0], parseInt(date[1]) - 1, date[2]))
        },

        methods: {
            addService: function () {
                this.services.push({
                    tooth: 1,
                    product_id: null
                });
            },

            removeService: function (index) {
                this.services.splice(index, 1);
            },

            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/user/service/' + this.data.public_id, {
                    services: this.services,
                    date: this.date
                })
                    .then((res) => {
                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                        this.loading = false;
                    })
            },

            range: function (start, end) {
                let array = [];

                for (let x = start; x <= end; x++) {
                    array.push(x);
                }

                return array;
            },

            setDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.date = year + '-' + month + '-' + day;
            },

            changeDate: function (date) {
                this.loading = true;

                this.setDate(date);
                location.href = location.pathname + '?date=' + this.date;
            }
        }
    }
</script>