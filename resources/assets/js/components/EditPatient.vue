<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Actualizar paciente
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form
                                    novalidate
                                    @submit.prevent="validateForm()"
                                    >

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Telefono</label>
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Telefono del paciente"
                                                    id="phone"
                                                    name="phone"
                                                    v-model="form.phone"
                                                    v-validate
                                                    data-vv-rules="required|regex:^[0-9]{10}$"
                                                    maxlength="10"
                                                    @keyup="phoneError = false"
                                                    :class="{'input-error': errors.has('phone') || phoneError}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('phone', 'required')">
                                                Campo requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('phone', 'regex')">
                                                Formato invalido
                                            </p>
                                            <p class="error" v-if="! errors.has('phone') && phoneError">
                                                Este telefono ya esta registrado
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Nombre del paciente"
                                                    id="name"
                                                    name="name"
                                                    v-model="form.name"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    :class="{'input-error': errors.has('name')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('name', 'required')">
                                                Campo requerido
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Email</label>
                                            <input
                                                    type="email"
                                                    class="form-control"
                                                    placeholder="Email del paciente"
                                                    id="email"
                                                    name="email"
                                                    v-model="form.email"
                                                    v-validate
                                                    data-vv-rules="required|email"
                                                    :class="{'input-error': errors.has('email')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('email', 'required')">
                                                Campo requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('email', 'email')">
                                                Formato invalido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <button class="btn btn-warning" v-bind:disabled="loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Actualizar paciente
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de cotizaciones -->
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list"></i>
                        Lista de cotizaciones
                    </h1>
                </div>
            </div>
            <div class="row edit-patient__budgets">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <div class="row">

                                <div class="col-sm-3" v-for="budget in form.budgets.data" v-if="form.budgets.data.length > 0">

                                    <div class="panel panel-info">
                                        <div class="panel-body">
                                            <h4>
                                                #{{ budget.public_id }}
                                            </h4>
                                        </div>
                                        <div class="panel-footer">
                                            <h5>
                                                <strong>Monto:</strong><br>
                                                ${{ budget.total_footer_value }} USD
                                            </h5>
                                            <h5>
                                                <strong>Generada:</strong><br>
                                                {{ budget.created_at | date }}
                                            </h5>
                                            <div class="text-center">
                                                <a
                                                        :href="'/user/budget/' + budget.public_id + '/edit'"
                                                        class="btn btn-info"
                                                    >
                                                    <i class="glyphicon glyphicon-eye-open"></i>
                                                    Ver detalle
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-12" v-if="form.budgets.data.length === 0">
                                    <p>
                                        No se ha generado cotizaciones.
                                        <a href="/user/budget/create">
                                            Generar cotización
                                        </a>
                                    </p>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xs-12" v-if="pagination.total > pagination.per_page">
                    <ul class="pagination">
                        <li :class="{'disabled': pagination.current === 1}">
                            <a :href="pagination.path + '?page=1'" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <li
                                v-for="i in range(1, pagination.last_page)"
                                :class="{'active': i === pagination.current}"
                            >
                            <a :href="pagination.path + '?page=' + i">
                                {{ i }}
                            </a>
                        </li>


                        <li :class="{'disabled': pagination.current === pagination.last_page}">
                            <a :href="pagination.path + '?page=' + pagination.last_page" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props: ['patient'],

        data: function () {
            return {
                loading: false,
                phoneError: false,
                form: {},
                pagination: {
                    current: null,
                    from: null,
                    to: null,
                    path: null,
                    last_page: null,
                    total: null,
                    per_page: null
                }
            }
        },

        beforeMount: function () {
            this.form = JSON.parse(this.patient);
            this.generatePagination();
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.verifyPhone();
                    }
                });
            },

            verifyPhone: function () {
                this.loading = true;

                axios.get('/user/patient/phone/' + this.form.phone + '/' + this.form.public_id)
                    .then((res) => {
                        if (res.data.success) {

                            if (res.data.valid) {
                                this.sendForm();

                                return res
                            }

                            this.loading = false;
                            this.phoneError = true;
                        }
                    })
                    .catch((err) => {
                        this.loading = false;
                    })
                ;
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/user/patient/' + this.form.public_id, this.form)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {

                        this.loading = false;
                    })
                ;
            },

            generatePagination: function () {
                this.pagination.current = this.form.budgets.current_page;
                this.pagination.from = this.form.budgets.from;
                this.pagination.to = this.form.budgets.to;
                this.pagination.path = this.form.budgets.path;
                this.pagination.last_page = this.form.budgets.last_page;
                this.pagination.total = this.form.budgets.total;
                this.pagination.per_page = this.form.budgets.per_page;
            },

            range: function (start, end) {
                let array = [];

                for (let x = start; x <= end; x++) {
                    array.push(x);
                }

                return array;
            }
        },
        filters: {
            date: function (value) {
                let date = value.split(' ');
                date = date[0].split('-');

                date = date[1] + '/' + date[2] + '/' +date[0];

                return date;
            }
        }
    }
</script>