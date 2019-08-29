<template>
    <div class="container edit-service">
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
            <div class="col-xs-12">
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

                                    Cambiar paciente
                                </a>
                                <hr>
                            </div>
                        </div>

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
                                            v-model="initDate"
                                            @input="changeDate($event)"
                                            :disabled="disabledDates"
                                            ></datepicker>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="diagnostic">Diagnosticado por</label>
                                    <select
                                            name="diagnostic"
                                            id="diagnostic"
                                            class="form-control"
                                            :class="{'input-error': errors.has('diagnostic')}"
                                            v-model="diagnostic"
                                            v-validate
                                            data-vv-rules="required"
                                            >
                                        <option
                                                v-for="doctor in doctors"
                                                :value="doctor.id"
                                                >
                                            {{ doctor.name }}
                                        </option>
                                    </select>
                                    <p class="error" v-if="errors.firstByRule('diagnostic', 'required')">
                                        Campo requerido
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-xs-12">
                                <h4 class="bg-info text-info">Servicios</h4>

                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th width="20%">Servicio</th>
                                            <th width="20%">Asistente</th>
                                            <th width="10%">Qty</th>
                                            <th width="15%">Diente</th>
                                            <th width="15%">Precio</th>
                                            <th width="15%">Total</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody v-for="(service, id) in services">
                                        <tr>
                                            <td>
                                                <select
                                                        :name="'product' + id"
                                                        :id="'product' + id"
                                                        class="form-control"
                                                        v-model="service.product_id"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('product' + id)}"
                                                        @change="changeProduct(service, id)"
                                                        :disabled="! user.hasRole.admin && service.doctor_id !== user.id"
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
                                                        :name="'assistant' + id"
                                                        :id="'assistant' + id"
                                                        class="form-control"
                                                        :class="{'input-error': errors.has('assistant' + id)}"
                                                        v-model="service.assistant_id"
                                                        v-validate
                                                        data-vv-rules="required"
                                                    >
                                                    <option
                                                            v-for="assistant in assistantUsers"
                                                            :value="assistant.id"
                                                        >
                                                        {{ assistant.name }}
                                                    </option>
                                                </select>
                                                <p class="error" v-if="errors.firstByRule('assistant' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <input
                                                        type="number"
                                                        class="form-control"
                                                        :name="'qty' + id"
                                                        :id="'qty' + id"
                                                        v-model="service.qty"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('qty' + id)}"
                                                        :disabled="!service.product_id || (! user.hasRole.admin && service.doctor_id !== user.id)"
                                                        >
                                                <p class="error" v-if="errors.firstByRule('qty' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <input
                                                        type="text"
                                                        :name="'tooth' + id"
                                                        :id="'tooth' + id"
                                                        class="form-control"
                                                        v-model="service.tooth"
                                                        :disabled="! user.hasRole.admin && service.doctor_id !== user.id"
                                                >
                                            </td>
                                            <td>
                                                <input
                                                        type="number"
                                                        class="form-control"
                                                        :name="'price' + id"
                                                        :id="'price' + id"
                                                        v-model="service.unit_price"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('price' + id)}"
                                                        :disabled="!service.product_id || (! user.hasRole.admin && service.doctor_id !== user.id)"
                                                >
                                                <p class="error" v-if="errors.firstByRule('price' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>{{ service.unit_price * service.qty }}</td>
                                            <td>
                                                <a @click="removeService(id)" v-if="user.hasRole.admin || service.doctor_id === user.id">
                                                    X
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="8">
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

                        <div class="row" v-if="anyRequiredLab()">

                            <div class="col-xs-12">
                                <h4 class="bg-info text-info">Enviar al laboratorio</h4>

                                <table class="table table-responsive">
                                    <thead>
                                    <tr>
                                        <th width="20%">Servicio</th>
                                        <th width="20%">Laboratorio</th>
                                        <th width="20%">Responsable</th>
                                        <th width="20%">Fecha solicitud entrega</th>
                                        <th width="20%" colspan="2">Hora de entrega</th>
                                    </tr>
                                    </thead>
                                    <tbody v-for="(service, id) in services">
                                        <tr v-if="requiredLab(service)">
                                            <td>
                                                <select
                                                        class="form-control"
                                                        v-model="service.product_id"
                                                        disabled
                                                        >
                                                    <option
                                                            v-for="product in productList"
                                                            :value="product.id"
                                                            >
                                                        {{ product.name }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'supplier' + id"
                                                        :id="'supplier' + id"
                                                        class="form-control"
                                                        :class="{'input-error': errors.has('supplier' + id)}"
                                                        v-model="service.supplier_id"
                                                        v-validate
                                                        :data-vv-rules="! authUser.hasRole.admin ? 'required' : ''"
                                                        >
                                                    <option
                                                            v-for="supplier in suppliers"
                                                            :value="supplier.id"
                                                            >
                                                        {{ supplier.name }}
                                                    </option>
                                                </select>
                                                <p class="error" v-if="errors.firstByRule('supplier' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'responsible' + id"
                                                        :id="'responsible' + id"
                                                        class="form-control"
                                                        :class="{'input-error': errors.has('responsible' + id)}"
                                                        v-model="service.responsible_id"
                                                        v-validate
                                                        :data-vv-rules="! authUser.hasRole.admin ? 'required' : ''"
                                                        >
                                                    <option
                                                            v-for="assistant in assistantUsers"
                                                            :value="assistant.id"
                                                            >
                                                        {{ assistant.name }}
                                                    </option>
                                                </select>
                                                <p class="error" v-if="errors.firstByRule('responsible' + id, 'required')">
                                                    Campo requerido
                                                </p>
                                            </td>
                                            <td>
                                                <datepicker
                                                        :name = "'delivery_date' + id"
                                                        :id = "'delivery_date' + id"
                                                        language="es"
                                                        input-class = "form-control"
                                                        format = "MM/dd/yyyy"
                                                        v-model="service.datePicker"
                                                        @input="service.delivery_date = updateDate($event)"
                                                        :disabled="disabledDates"
                                                        ></datepicker>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'hour' + id"
                                                        :id="'hour' + id"
                                                        class="form-control"
                                                        v-model="service.hour"
                                                        v-validate
                                                        :data-vv-rules="! authUser.hasRole.admin ? 'required' : ''"
                                                        :class="{'input-error': errors.has('hour' + id)}"
                                                        >
                                                    <option
                                                            v-for="i in 24"
                                                            :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                            >
                                                        {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                                    </option>
                                                </select>

                                                <p class="error" v-if="errors.firstByRule('hour' + id, 'required')">
                                                    Requerido
                                                </p>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'minute' + id"
                                                        :id="'minute' + id"
                                                        class="form-control"
                                                        v-model="service.minute"
                                                        v-validate
                                                        :data-vv-rules="! authUser.hasRole.admin ? 'required' : ''"
                                                        :class="{'input-error': errors.has('minute' + id)}"
                                                        >
                                                    <option
                                                            v-for="i in 60"
                                                            :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                            >
                                                        {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                                    </option>
                                                </select>

                                                <p class="error" v-if="errors.firstByRule('minute' + id, 'required')">
                                                    Requerido
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="row">

                            <div class="col-xs-12">
                                <h4 class="bg-info text-info">Notas</h4>

                                <table class="table table-responsive">
                                    <thead>
                                        <tr>
                                            <th>Notas</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(note,id) in notes">
                                            <td>
                                                <textarea
                                                        cols="30"
                                                        rows="3"
                                                        class="form-control"
                                                        placeholder="Notas del paciente"
                                                        v-model="note.content"
                                                        ></textarea>
                                            </td>
                                            <td>
                                                <a @click="removeNote(id)">
                                                    X
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <button class="btn btn-success" @click="addNote()">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Nota
                                                </button>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <h3>
                                    Imagenes y radiografias
                                    <a 
                                        :href="'/user/service/' + this.data.public_id + '/uploadImage'" 
                                        target="_blank"
                                        v-if="authUser.upload_alternative && false"
                                        >
                                        ¿Carga lenta? prueba la carga basica
                                    </a>
                                </h3>
                            </div>
                        </div>

                        <div class="row">

                            <div class="col-sm-4 space-image" v-for="(image, id) in images">
                                <img :src="image" class="img-responsive images">

                                <button class="btn btn-danger btn-sm" @click="removeImage(id)">
                                    <i class="glyphicon glyphicon-remove"></i>
                                </button>
                            </div>

                            <div class="col-sm-4">
                                <!-- Cargar imagen -->

                                <form
                                        id="alternativeForm"
                                        enctype="multipart/form-data"
                                        method="post"
                                        :action="'/user/service/' + data.public_id"
                                        v-show="false"
                                >
                                    <input type="hidden" name="_method" value="PUT">
                                    <input type="hidden" name="_token" :value="token">
                                    <input type="hidden" name="data" :value="dataAlternative">

                                    <input
                                            v-for="i in (images.length + 1)"
                                            :key="i"
                                            type="file"
                                            name="image[]"
                                            :id="'image' + i"
                                            class="form-control"
                                            placeholder="Imagen"
                                            @change="setCapture()"
                                            v-validate
                                            data-vv-rules="mimes:image/jpeg,image/png|size:5120"
                                    >
                                </form>

                                <img
                                        id="selectImage"
                                        src="/img/camera.png"
                                        alt="Agregar imagen"
                                        class="img-responsive"
                                        @click="loadImage">

                                <p class="error" v-if="errors.firstByRule('image', 'size')">
                                    Maximo 5 mb
                                </p>
                                <p class="error" v-if="errors.firstByRule('image', 'mimes')">
                                    Imagen necesita ser formato .png o .jpg
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12 text-center">
                                <hr>
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
                                    <tr v-for="patient in modal.data" v-if="patient.public_id !== data.public_id">
                                        <td>{{ patient.phone }}</td>
                                        <td>{{ patient.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectPatient(patient)"
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
        props: [
            'patient',
            'products',
            'historyDate',
            'currentUser',
            'assistants',
            'suppliers',
            'authUser',
            'doctors'
        ],
        components: {
            Datepicker
        },

        data: function () {
            return {
                loading: false,
                data: {},
                productList: [],
                services: [],
                assistantUsers: [],
                date: '',
                initDate: '',
                notes: [],
                images: [],
                diagnostic: null,
                user: '',
                modal: {
                    data: [],
                    loading: false,
                    search: ''
                },
                disabledDates: {},
                dataAlternative: {},
                token: null
            }
        },
        mounted: function () {
            this.data = JSON.parse(this.patient);
            this.productList = JSON.parse(this.products);
            this.services = this.data.patient_history;
            this.user = JSON.parse(this.currentUser);
            this.assistantUsers = JSON.parse(this.assistants);

            let date = this.historyDate.split('-');
            date = new Date(parseInt(date[0]), parseInt(date[1]) - 1, parseInt(date[2]));

            this.initDate = date;
            this.setDate(date);

            this.notes = this.data.notes;

            const today = new Date();
            const yesterday = new Date(today.getTime() - 24*60*60*1000);
            this.disabledDates = this.user.hasRole.admin ? {} : {to: yesterday};

            this.token = document.head.querySelector('meta[name="csrf-token"]').content;
        },

        methods: {
            addService: function () {
                const date = new Date();

                this.services.push({
                    tooth: null,
                    product_id: null,
                    doctor_id: this.user.id,
                    assistant_id: null,
                    unit_price: null,
                    qty: null,
                    responsible_id: null,
                    supplier_id: null,
                    datePicker: date,
                    delivery_date: this.updateDate(date),
                    hour: '00',
                    minute: '00',
                    diagnostic_id: null
                });
            },

            addNote: function () {
                this.notes.push({
                    content: ''
                });
            },

            removeService: function (index) {
                this.services.splice(index, 1);
            },

            removeNote: function (index) {
                this.notes.splice(index, 1);
            },

            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        //this.sendForm();

                        // Envio formulario sin ajax por problemas en la carga de imagenes
                        this.loading = true;
                        this.dataAlternative = JSON.stringify({
                            services: this.services,
                            date: this.date,
                            notes: this.notes,
                            //images: this.images,
                            diagnostic: this.diagnostic
                        });

                        window.setTimeout(function () {
                            $('#alternativeForm').submit();
                        }, 500)
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/user/service/' + this.data.public_id, {
                    services: this.services,
                    date: this.date,
                    notes: this.notes,
                    images: this.images,
                    diagnostic: this.diagnostic
                })
                    .then((res) => {
                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
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
            },

            changeProduct: function (service, index) {
                for (let i in this.productList) {
                    if (this.productList[i].id == service.product_id) {
                        this.services[index].unit_price = this.productList[i].price;
                    }
                }
            },

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
                location.href = '/user/service/' + patient.public_id + '/edit?date=' + this.date;
            },

            setCapture: function() {
                const file = $('#image' + (this.images.length + 1))[0].files[0];

                if (file.type !== 'image/png' && file.type !== 'image/jpeg') {
                    return false;
                }

                const reader = new FileReader();

                reader.addEventListener('load', () => {
                    this.images.push(reader.result);
                });

                reader.readAsDataURL(file);
            },

            removeImage: function (index) {
                this.images.splice(index, 1);
            },

            requiredLab: function (service) {

                let requiredLab = false;

                this.productList.forEach(function (product) {
                    if (product.id === service.product_id && product.required_lab) {
                        requiredLab = true;
                    }
                });

                return requiredLab;
            },

            anyRequiredLab: function () {
                let anyRequiredLab = false;

                this.services.forEach((service) => {
                    if (this.requiredLab(service)) {
                        anyRequiredLab = true;
                    }
                });

                return anyRequiredLab;
            },

            updateDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                return year + '-' + month + '-' + day;
            },

            loadImage() {
                $('#image' + (this.images.length + 1)).click();
            }
        }
    }
</script>