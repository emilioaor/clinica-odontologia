<template>
    <div class="modal fade" :id="modalId" tabindex="-1" role="dialog" :aria-labelledby="modalId + 'Label'" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content" v-show="! searchPatient && ! searchService">
                <div class="modal-body">

                    <div class="row">
                        <div class="col-xs-6">
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
                                        :disabled="disabledDates"
                                        ></datepicker>
                            </div>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="supplier">Proveedor</label>
                                <select
                                        name="supplier"
                                        id="supplier"
                                        class="form-control"
                                        v-model="data.supplier_id"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('supplier')}"
                                        @change="changeSupplier()"
                                        >
                                    <option
                                            v-for="supplier in suppliers"
                                            :value="supplier.id"
                                            >
                                        {{ supplier.name }}
                                    </option>
                                </select>

                                <p class="error" v-if="errors.firstByRule('supplier', 'required')">
                                    Campo requerido
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row" v-if="supplier && (supplier.type === 8 || supplier.type === 9 || supplier.type === 6)">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="patient">Paciente</label>
                                <div class="input-group">
                                    <input  type="text"
                                        class="form-control"
                                        id="patient"
                                        name="patient"
                                        placeholder="Paciente"
                                        readonly
                                        v-model="patient.name"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('patient')}"
                                        >
                                        <span class="input-group-btn">
                                            <button
                                                    class="btn btn-info"
                                                    type="button"
                                                    @click="searchPatients()"
                                                    >
                                                <i class="glyphicon glyphicon-search"></i>
                                            </button>
                                        </span>
                                </div>

                                <p class="error" v-if="errors.firstByRule('patient', 'required')">
                                    Requerido
                                </p>
                            </div>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="service">Servicio</label>
                                <div class="input-group">
                                    <input  type="text"
                                        class="form-control"
                                        id="service"
                                        name="service"
                                        placeholder="Servicio"
                                        readonly
                                        v-model="patientHistory.description"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('service')}"
                                        >
                                        <span class="input-group-btn">
                                            <button
                                                    class="btn btn-info"
                                                    type="button"
                                                    @click="searchServices()"
                                                    :disabled="patient.public_id === null"
                                                    >
                                                <i class="glyphicon glyphicon-search"></i>
                                            </button>
                                        </span>
                                </div>

                                <p class="error" v-if="errors.firstByRule('service', 'required')">
                                    Requerido
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="expense">Gasto</label>
                                <input
                                        type="text"
                                        id="expense"
                                        name="expense"
                                        class="form-control"
                                        placeholder="Gasto"
                                        v-model="data.description"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('expense')}"
                                        >

                                <p class="error" v-if="errors.firstByRule('expense', 'required')">
                                    Campo requerido
                                </p>
                            </div>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="amount">Monto</label>
                                <input
                                        type="number"
                                        id="amount"
                                        name="amount"
                                        class="form-control"
                                        placeholder="Monto"
                                        v-model="data.amount"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('amount')}"
                                        >

                                <p class="error" v-if="errors.firstByRule('amount', 'required')">
                                    Campo requerido
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" @click="validate()" v-show="! loading">Registrar</button>
                    <button
                            type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            v-show="! loading"
                            :id="modalId + 'Close'"
                            >
                        Cerrar
                    </button>
                    <img src="/img/loading.gif" v-if="loading">
                </div>
            </div>

            <!-- Search patients -->
            <div class="modal-content" v-show="searchPatient">
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
                                <tr v-for="p in modal.data">
                                    <td>{{ p.phone }}</td>
                                    <td>{{ p.name }}</td>
                                    <td>
                                        <button
                                                class="btn btn-primary"
                                                @click="selectPatient(p)"
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
                    <button type="button" class="btn btn-secondary" @click="searchPatient = false">Cancelar</button>
                </div>
            </div>

            <!-- Search services -->
            <div class="modal-content" v-show="searchService">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3>
                                <strong>
                                    Selecciona servicio
                                </strong>
                            </h3>
                        </div>

                        <div class="col-xs-12">
                            <datepicker
                                    name = "serviceDate"
                                    id = "serviceDate"
                                    language="es"
                                    input-class = "form-control datepicker"
                                    format = "MM/dd/yyyy"
                                    v-model="modalService.datePicker"
                                    @input="changeDateService($event)"
                                    ></datepicker>
                        </div>
                    </div>
                    <hr>

                    <div class="row">

                        <div class="col-xs-12">

                            <table class="table table-responsive table-striped">
                                <thead>
                                <tr>
                                    <th width="30%">Código</th>
                                    <th width="50%">Producto</th>
                                    <th width="20%">Precio</th>
                                    <th></th>
                                </tr>
                                </thead>

                                <tbody v-if="! modalService.loading">
                                <tr v-for="s in modalService.data">
                                    <td>{{ s.public_id }}</td>
                                    <td>{{ s.product.name }}</td>
                                    <td>{{ '$' + s.price }}</td>
                                    <td>
                                        <button
                                                class="btn btn-primary"
                                                @click="selectService(s)"
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
                    <button  type="button" class="btn btn-secondary" @click="searchService = false">
                        Cancelar
                    </button>
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
            modalId: {
                type: String,
                required: true
            },
            user: {
                type: Object,
                required: true
            }
        },

        data: function () {
            return {
                loading: false,
                initDate: new Date(),
                data: {
                    date: '',
                    supplier_id: null,
                    description: null,
                    amount: null,
                    patient_history_id: null
                },
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                },
                modalService: {
                    data: [],
                    loading: false,
                    datePicker: new Date(),
                    date: null
                },
                suppliers: [],
                supplier: null,
                patient: {
                    name: null,
                    public_id: null
                },
                patientHistory: {
                    public_id: null,
                    description: null
                },
                searchPatient: false,
                searchService: false,
                disabledDates: {}
            }
        },

        mounted: function () {
            // Inicializa la fecha
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.date = year + '-' + month + '-' + day;
            this.modalService.date = year + '-' + month + '-' + day;

            // Obtiene los proveedores
            this.getSuppliers();

            const today = new Date();
            const yesterday = new Date(today.getTime() - 24*60*60*1000);
            this.disabledDates = this.user.hasRole.admin ? {} : {to: yesterday}
        },

        methods: {
            changeDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.date = year + '-' + month + '-' + day;
            },

            getSuppliers: function () {
                axios.get('/user/supplier/list')
                    .then((res) => {
                        if (res.data.success) {
                            this.suppliers = res.data.suppliers;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                    })
            },

            changeSupplier: function () {
                this.suppliers.forEach((supplier) => {
                    if (supplier.id === this.data.supplier_id) {
                        this.supplier = supplier;
                    }
                });

                this.data.patient_history_id = null;
                this.patient.name = null;
                this.patient.public_id = null;
                this.patientHistory.public_id = null;
            },

            searchPatients: function () {
                this.modal.data = [];
                this.modal.loading = true;
                this.searchPatient = true;

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
                this.patient.name = patient.name;
                this.patient.public_id = patient.public_id;
                this.searchPatient = false;
            },

            searchServices: function () {

                this.modalService.data = [];
                this.modalService.loading = true;
                this.searchService = true;

                const public_id = this.patient.public_id;

                axios.get('/user/service/' + public_id + '/search?start=' + this.modalService.date + '&end=' + this.modalService.date)
                    .then((res) => {
                        this.modalService.loading = false;

                        for (let x in res.data.data) {

                            for (let y in res.data.data[x].services) {

                                this.modalService.data.push(res.data.data[x].services[y]);
                            }
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.modalService.loading = false;
                    })
                ;
            },

            changeDateService: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.modalService.date = year + '-' + month + '-' + day;

                this.searchServices();
            },

            selectService: function (service) {
                this.patientHistory.public_id = service.public_id;
                this.patientHistory.description = service.product.name;
                this.searchService = false;
                this.data.patient_history_id = service.id;
            },

            validate: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.post('/user/expense', [this.data])
                    .then((res) => {

                        this.loading = false;

                        if (res.data.success) {

                            $('#' +  this.modalId + 'Close').click();

                            this.data.supplier_id = null;
                            this.data.description = null;
                            this.data.amount = null;
                            this.data.patient_history_id = null;
                            this.patient.name = null;
                            this.patient.public_id = null;
                            this.patientHistory.public_id = null;
                            this.supplier = null;

                            this.$emit('register');
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                    })
                ;
            }
        }
    }
</script>