<template>
    <section>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <div class="register-budget__logo" onclick="document.querySelector('#logo').click()">
                                        <h3 v-if="form.logo === ''">
                                            <i class="glyphicon glyphicon-plus"></i>
                                        </h3>
                                        <img v-bind:src="form.logo">
                                        <input type="file" id="logo" @change="uploadLogo()">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-8">

                                <form v-on:submit.prevent="validateForm()">
                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="business_name">Emisor para las cotizaciones</label>
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        name="business_name"
                                                        id="business_name"
                                                        placeholder="Emisor para las cotizaciones"
                                                        v-model="form.business_name"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        v-bind:class="{'input-error': errors.has('business_name')}"
                                                        >
                                                <p class="error" v-if="errors.firstByRule('business_name', 'required')">
                                                    Campo requerido
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="business_name">Telefono de contacto</label>
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        name="phone"
                                                        id="phone"
                                                        placeholder="Telefono de contacto"
                                                        v-model="form.phone"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        v-bind:class="{'input-error': errors.has('phone')}"
                                                        >
                                                <p class="error" v-if="errors.firstByRule('phone', 'required')">
                                                    Campo requerido
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="business_name">Email</label>
                                                <input
                                                        type="email"
                                                        class="form-control"
                                                        name="email"
                                                        id="email"
                                                        placeholder="Email"
                                                        v-model="form.email"
                                                        v-validate
                                                        data-vv-rules="required|email"
                                                        v-bind:class="{'input-error': errors.has('email')}"
                                                        >
                                                <p class="error" v-if="errors.firstByRule('email', 'required')">
                                                    Campo requerido
                                                </p>
                                                <p class="error" v-if="errors.firstByRule('email', 'email')">
                                                    Formato invalido
                                                </p>
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="business_name">Dirección corta</label>
                                                <input
                                                        type="text"
                                                        class="form-control"
                                                        name="address"
                                                        id="address"
                                                        placeholder="Dirección"
                                                        v-model="form.address"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        v-bind:class="{'input-error': errors.has('address')}"
                                                        >
                                                <p class="error" v-if="errors.firstByRule('address', 'required')">
                                                    Campo requerido
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <img src="/img/loading.gif" v-if="loading">
                                                <button class="btn btn-warning" v-if="!loading">
                                                    <i class="glyphicon glyphicon-saved"></i>
                                                    Actualizar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        props: ['user'],
        data: function () {
            return {
                loading: false,
                form: {
                    logo: '',
                    business_name: '',
                    phone: '',
                    email: '',
                    address: ''
                }
            }
        },

        mounted: function () {
            const json = JSON.parse(this.user);

            if (json.logo !== null && json.logo !== '') {
                this.form.logo = '/uploads/' + json.logo;
            }

            this.form.business_name = json.business_name;
            this.form.phone = json.phone;
            this.form.email = json.email;
            this.form.address = json.address;
        },

        methods: {
            uploadLogo: function () {
                const file = $('#logo')[0].files[0];
                const reader = new FileReader();

                reader.addEventListener('load', () => {
                    this.form.logo = reader.result;

                    this.upload();
                });

                reader.readAsDataURL(file);
            },

            upload: function () {
                axios.post('/user/config/uploadLogo', {logo: this.form.logo})
                        .then((res) => {

                    if (! res.data.success) {
                        this.form.logo = '';
                    }

                })
                .catch((err) => {
                    alert('Error al cargar imagen, intente nuevamente');
                    this.form.logo = '';
                })
                ;
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

                axios.put('/user/config/business', this.form)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        this.loading = false;
                    })
            }
        }
    }
</script>