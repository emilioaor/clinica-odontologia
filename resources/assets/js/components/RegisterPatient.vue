<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Registrar paciente
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <form
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
                                                    @keyup="verifyPhone()"
                                                    :class="{'input-error': errors.has('phone') || phoneError}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('phone', 'required')">
                                                Campo requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('phone', 'regex')">
                                                Formato invalido
                                            </p>
                                            <p class="error" v-if="! errors.has('phone') && phoneError">
                                                Este paciente ya esta registrado
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
                                                    :disabled="phoneError"
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
                                                    :disabled="phoneError"
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

                                <div class="row" v-if="! phoneError">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="name">Referencia</label>

                                            <select
                                                    name="patient_reference_id"
                                                    id="patient_reference_id"
                                                    class="form-control"
                                                    v-model="form.patient_reference_id"
                                                    >
                                                <option :value="null">Ninguno</option>
                                                <option
                                                        v-for="reference in patientReferences"
                                                        :key="reference.id"
                                                        :value="reference.id"
                                                        >
                                                    {{ reference.description }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="cancel_appointment">Bloquear citas</label>

                                            <p>
                                                <input
                                                        type="checkbox"
                                                        name="cancel_appointment"
                                                        id="cancel_appointment"
                                                        v-model="form.cancel_appointment"
                                                        >
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4" v-if="user.hasRole.sell_manager">
                                        <div class="form-group">
                                            <label for="cancel_appointment">Registrar llamada</label>

                                            <p>
                                                <input
                                                        type="checkbox"
                                                        name="register_call"
                                                        id="register_call"
                                                        v-model="form.register_call"
                                                >
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <h3>
                                            Fotografía del paciente
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
                                                    v-show="false"
                                                    data-vv-rules="mimes:image/jpeg,image/png|size:5120"
                                            >
                                        

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
                                </div><br>

                                <div class="row">
                                    <div class="col-xs-12" v-if="! phoneError">
                                        <a class="btn btn-success" @click="sendForm()">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Registar paciente
                                        </a>
                                    </div>

                                    <div class="col-xs-12" v-if="phoneError">
                                        <a class="btn btn-primary"
                                            :href="'/user/service/1/edit'">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Registrar servicio
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a data-toggle="modal" id="patientModalButton" data-target="#patientModal" class="hidden"></a>
        <!-- Modal -->
        <div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="alert alert-success">
                            <p>
                                <strong>ATENCIÓN:</strong> El paciente <strong>{{ form.name }}</strong> ya se encuentra
                                registrado. Puede indicar los servicios prestados sin necesidad de repetir este paso
                            </p>
                            <p>
                                ¿Desea registrar servicios a este paciente?
                            </p>
                        </div>

                        <a 
                            class="btn btn-success"
                            v-if="patient"
                            :href="'/user/service/' + patient.public_id + '/edit'"
                        >
                            <i class="glyphicon glyphicon-plus"></i>
                            SI
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">NO</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props: {
            patientReferences: {
                type: Array,
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
                phoneError: false,
                images: [],
                form: {
                    name: '',
                    phone: '',
                    email: '',
                    patient_reference_id: null,
                    cancel_appointment: false,
                    register_call: false,
                    patientImage: ''
                },
                patient: null,
                dataAlternative: {},
                token: null
            }
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.loading = true;
                        this.verifyPhone(true)
                        this.loading = true;

                        window.setTimeout(function () {
                            $('#patientForm').submit();
                        }, 500)
                    }
                });
            },

            verifyPhone: function (sendForm = false) {
                if (this.form.phone.length < 5) {
                    return false
                }

                axios.get('/user/patient/phone/' + this.form.phone)
                    .then((res) => {
                        if (res.data.success) {

                            if (! res.data.phoneError) {
                                this.phoneError = false;

                                if (sendForm) {
                                    //this.sendForm()
                                }

                                if (res.data.isPatient) {
                                    return
                                }
                            }

                            this.loading = false;
                            this.phoneError = res.data.phoneError;
                            this.patient = res.data.patient;
                            this.form.name = res.data.patient.name;
                            this.form.email = res.data.patient.email;

                            if (this.phoneError) {
                                $('#patientModalButton').click();
                            }
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                           // location.href = '/';
                        }
                        this.loading = false;
                        this.phoneError = false;
                        console.log(err)
                    })
                ;
            },

            sendForm: function () {
                this.loading = true;
                let formData = new FormData()
                formData.append('name', this.form.name)
                formData.append('phone', this.form.phone)
                formData.append('email', this.form.email)
                formData.append('patient_reference_id', this.form.patient_reference_id)
                formData.append('cancel_appointment', this.form.cancel_appointment)
                formData.append('register_call', this.form.register_call)
                formData.append('image', this.form.patientImage)

                axios.post('/user/patient', formData)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }console.log(res)
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            //location.href = '/';
                        }
                        console.log(err.response.data)
                        this.loading = false;
                    })
                ;
            },
            setCapture: function(e) {
                const file = $('#image' + (this.images.length + 1))[0].files[0];
                this.form.patientImage = file
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
            loadImage() {
                $('#image' + (this.images.length + 1)).click();
            }
        }
    }
</script>

<style scoped>
    .patientModal {
        font-size: 25px;
    }
</style>
