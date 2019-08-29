<template>
    <div class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-calendar" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Registrar cita
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-12">
                                <a
                                        data-toggle="modal"
                                        data-target="#patientModal"
                                        @click="searchPatients()"
                                        >
                                    <i class="glyphicon glyphicon-search"></i>

                                    <span v-if="! patient">
                                        Seleccionar paciente
                                    </span>
                                    <span v-if="patient">
                                        Cambiar paciente
                                    </span>
                                </a>
                                <hr>
                            </div>
                        </div>

                        <section v-if="patient">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <p>
                                            {{ patient.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Telefono</label>
                                        <p>
                                            {{ patient.phone }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <p>
                                            {{ patient.email }}
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="date">Fecha</label>
                                        <datepicker
                                                name = "date"
                                                id = "date"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changeDate($event)"
                                                v-model="initDate"
                                                ></datepicker>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="hour">Hora</label>
                                        <select
                                                name="hour"
                                                id="hour"
                                                class="form-control"
                                                v-model="form.hour"
                                                v-validate
                                                data-vv-rules="required"
                                                :class="{'input-error': errors.has('hour')}"
                                                >
                                            <option
                                                    v-for="i in 24"
                                                    :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                    :key="i"
                                                    >
                                                {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                            </option>
                                        </select>

                                        <p class="error" v-if="errors.firstByRule('hour', 'required')">
                                            Requerido
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="minute">Minuto</label>
                                        <select
                                                name="minute"
                                                id="minute"
                                                class="form-control"
                                                v-model="form.minute"
                                                v-validate
                                                data-vv-rules="required"
                                                :class="{'input-error': errors.has('minute')}"
                                                >
                                            <option
                                                    v-for="i in 60"
                                                    :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                    :key="i"
                                                    >
                                                {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                            </option>
                                        </select>

                                        <p class="error" v-if="errors.firstByRule('minute', 'required')">
                                            Requerido
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="doctor">Doctor</label>
                                        <div class="input-group">
                                            <input  type="text"
                                                    class="form-control"
                                                    id="doctor"
                                                    name="doctor"
                                                    placeholder="Doctor"
                                                    :value="!doctor ? '': doctor.name"
                                                    v-validate
                                                    data-vv-rules=""
                                                    :class="{'input-error': errors.has('doctor')}"
                                                    readonly
                                                    >
                                                <span class="input-group-btn">
                                                    <button
                                                            class="btn btn-info"
                                                            type="button"
                                                            data-toggle="modal"
                                                            data-target="#doctorModal"
                                                            @click="searchDoctor();"
                                                            >
                                                        <i class="glyphicon glyphicon-search"></i>
                                                    </button>
                                                </span>
                                        </div>
                                        <p class="error" v-if="errors.firstByRule('doctor', 'required')">
                                            Requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Tratamiento</th>
                                                <th width="5%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(detail, index) in form.details" :key="index">
                                                <td>
                                                    <select
                                                            :name="'product' + index"
                                                            :id="'product' + index"
                                                            class="form-control"
                                                            v-model="detail.product_id"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('product' + index)}"
                                                            >
                                                        <option
                                                                v-for="product in products"
                                                                :key="product.id"
                                                                :value="product.id"
                                                                v-show="! hasSelectedProduct(product.id)"
                                                                >
                                                            {{ product.name }}
                                                        </option>
                                                    </select>

                                                    <p class="error" v-if="errors.firstByRule('product' + index, 'required')">
                                                        Requerido
                                                    </p>
                                                </td>
                                                <td class="text-center">
                                                    <a @click="removeDetail(index)">X</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>
                                                    <button
                                                            class="btn btn-success" @click="addDetail()"
                                                            v-if="this.form.details.length < this.products.length"
                                                            >
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
                                    <button
                                            type="button"
                                            class="btn btn-primary btn-lg"
                                            v-if="!loading"
                                            @click="validateForm()">
                                        <i class="glyphicon glyphicon-saved"></i>
                                        Guardar
                                    </button>

                                    <img src="/img/loading.gif" v-if="loading">
                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>
                                    <strong>
                                        Selecciona al paciente
                                    </strong>
                                </h3>
                            </div>
                            <div class="col-sm-6">
                                <h3 class="text-right">
                                    <a href="/user/patient/create">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        Registrar paciente
                                    </a>
                                </h3>
                            </div>
                            <div class="col-xs-12">
                                <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Buscador"
                                        v-model="modal.search"
                                        @keyup="searchPatients()"
                                        >
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-xs-12">

                                <table class="table table-responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th width="50%">Telefono</th>
                                        <th width="50%">Nombre</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="! modal.loading">
                                    <tr
                                            v-for="p in modal.data"
                                            :key="p.id"
                                            v-if="!patient || patient.id !== p.id"
                                            :class="{'lock-patient': p.cancel_appointment}"
                                            >
                                        <td>{{ p.phone }}</td>
                                        <td>{{ p.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectPatient(p)"
                                                    data-dismiss="modal"
                                                    v-if="! p.cancel_appointment"
                                                    >
                                                <i class="glyphicon glyphicon-ok"></i>
                                            </button>

                                            <p class="text-center" v-if="p.cancel_appointment">
                                                <i class="glyphicon glyphicon-ban-circle"></i>
                                            </p>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="doctorModal" tabindex="-1" role="dialog" aria-labelledby="doctorModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>
                                    <strong>
                                        Selecciona al doctor
                                    </strong>
                                </h3>
                            </div>
                            <div class="col-xs-12">
                                <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Buscador"
                                        v-model="modal.search"
                                        @keyup="searchDoctor()"
                                        >
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-xs-12">

                                <table class="table table-responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th width="33%">Telefono</th>
                                        <th width="33%">Nombre</th>
                                        <th width="33%">Email</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="! modal.loading">
                                    <tr v-for="d in modal.data" :key="d.id" v-if="!doctor || doctor.id !== d.id">
                                        <td>{{ d.phone }}</td>
                                        <td>{{ d.name }}</td>
                                        <td>{{ d.email }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectDoctor(d)"
                                                    data-dismiss="modal"
                                                    >
                                                <i class="glyphicon glyphicon-ok"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        components: {
            Datepicker
        },
        props: {
            products: {
                type: Array,
                required: true
            }
        },
        data: function () {
            return {
                loading: false,
                initDate: new Date(),
                patient: null,
                doctor: null,
                form: {
                    date: null,
                    patient_id: null,
                    doctor_id: null,
                    hour: null,
                    minute: null,
                    details: []
                },
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                }
            }
        },

        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.form.date = year + '-' + month + '-' + day;
        },

        methods: {
            searchPatients: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/user/patient/budget/search?search=' + this.modal.search)
                        .then((res) => {
                    this.modal.loading = false;

                    this.modal.data = res.data.patients;
                })
                .catch((err) => {
                    if (err.response.status === 403 || err.response.status === 405) {
                        location.href = '/';
                    }
                    this.modal.loading = false;
                })
                ;
            },

            selectPatient: function (patient) {
                this.patient = patient;
                this.form.patient_id = patient.id;
            },

            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                })
            },

            sendForm: function () {

                this.loading = true;

                axios.post('/user/appointment', this.form)
                        .then((res) => {
                            if (res.data.success) {
                                location.href = res.data.redirect;
                            }
                        })
                        .catch((err) => {
                            if (err.response.status === 403 || err.response.status === 405) {
                                location.href = '/';
                            }
                            this.loading = false;

                            console.log('Error', err);
                        })
                ;
            },

            changeDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.form.date = year + '-' + month + '-' + day;
            },

            addDetail: function () {
                this.form.details.push({
                    product_id: null
                });
            },

            hasSelectedProduct: function (productId) {
                let hasProductInDetail = false;

                this.form.details.forEach((detail) => {
                    if (detail.product_id === productId) {
                        hasProductInDetail = true;
                    }
                });

                return hasProductInDetail;
            },

            removeDetail: function (index) {
                if (this.form.details.length > 1) {
                    this.form.details.splice(index, 1);
                }
            },

            searchDoctor: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/user/user/search?level=doctor&search=' + this.modal.search)
                        .then((res) => {
                    this.modal.loading = false;

                    this.modal.data = res.data.users;
                })
                .catch((err) => {
                    if (err.response.status === 403 || err.response.status === 405) {
                        location.href = '/';
                    }
                    this.modal.loading = false;
                })
                ;
            },

            selectDoctor: function (doctor) {
                this.doctor = doctor;
                this.form.doctor_id = doctor.id;
                this.errors.remove('doctor');
            },
        }
    }
</script>

<style scoped>
    .lock-patient {
        background-color: #f2dede !important;
        color: rgba(0,0,0,.5);
    }
    .lock-patient p {
        margin: .3rem 0 .1rem;
    }
</style>