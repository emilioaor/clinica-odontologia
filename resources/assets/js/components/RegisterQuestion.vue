<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Registrar pregunta
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <form v-on:submit.prevent="validateForm()">

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Para</label>
                                        <div v-for="(d, index) in form.doctors">

                                            <div class="row">
                                                <div class="col-xs-11">
                                                    <select
                                                            :name="'to_id' + index"
                                                            :id="'to_id' + index"
                                                            class="form-control"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            v-model="d.to_id"
                                                            v-bind:class="{'input-error': errors.has('to_id' + index)}"
                                                            >
                                                        <option
                                                                v-for="doctor in doctors"
                                                                v-show="! hasDoctorSelected(doctor.id)"
                                                                :value="doctor.id">
                                                            {{ doctor.name }}
                                                        </option>
                                                    </select>
                                                    <p class="error" v-if="errors.firstByRule('to_id' + index, 'required')">
                                                        Este campo es requerido
                                                    </p>
                                                </div>

                                                <div class="col-xs-1">
                                                    <button type="button" class="btn btn-danger" @click="removeDoctor(index)">
                                                        <i class="glyphicon glyphicon-remove"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group" v-if="form.doctors.length < doctors.length">
                                        <a @click="addDoctor()">
                                            <i class="glyphicon glyphicon-plus"></i>
                                            Agregar destinatario
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('title')}">
                                        <label for="title">Titulo</label>
                                        <input
                                                type="text"
                                                name="title"
                                                id="title"
                                                class="form-control"
                                                v-validate
                                                data-vv-rules="required"
                                                v-model="form.title"
                                                v-bind:class="{'input-error': errors.has('title')}"
                                            >
                                        <p class="error" v-if="errors.firstByRule('title', 'required')">
                                            Este campo es requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('question')}">
                                        <label for="question">Pregunta</label>
                                        <textarea
                                                cols="30"
                                                rows="8"
                                                id="question"
                                                name="question"
                                                class="form-control"
                                                v-validate
                                                data-vv-rules="required"
                                                v-model="form.question"
                                                v-bind:class="{'input-error': errors.has('question')}"
                                        ></textarea>
                                        <p class="error" v-if="errors.firstByRule('question', 'required')">
                                            Este campo es requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-3" v-for="(attach, index) in form.attaches">
                                    <div class="form-group">
                                        <a @click="removeAttach(index)" class="text-danger">X</a>
                                        {{ attach.filename.length <= 15 ? attach.filename : attach.filename.substring(attach.filename.length - 15) }}
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <input type="file" id="attach" class="hide" @change="setAttach()">
                                        <a onclick="$('#attach').click()">
                                            <i class="glyphicon glyphicon-paperclip"></i>
                                            Adjuntar
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <button class="btn btn-success" v-if="!loading">
                                        <i class="glyphicon glyphicon-send"></i>
                                        Enviar
                                    </button>

                                    <img src="/img/loading.gif" v-if="loading">
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            doctors: {
                required: true,
                type: Array
            }
        },
        data: function () {
            return {
                loading: false,
                form: {
                    question: null,
                    title: null,
                    doctors: [{
                        to_id: null
                    }],
                    attaches: []
                }
            }
        },

        methods: {
            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                })
            },

            sendForm: function () {

                this.loading = true;

                axios.post('/user/question', this.form)
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

            addDoctor: function () {

                if (this.form.doctors.length >= this.doctors.length) {
                    return false;
                }

                this.form.doctors.push({
                    to_id: null
                });
            },

            removeDoctor: function (index) {

                if (this.form.doctors.length <= 1) {
                    return false;
                }

                this.form.doctors.splice(index, 1);
            },

            hasDoctorSelected: function (id) {
                for (let i in this.form.doctors) {

                    if (this.form.doctors[i].to_id === id) {
                        return true;
                    }
                }

                return false;
            },

            setAttach: function() {
                const file = $('#attach')[0].files[0];

                const reader = new FileReader();

                reader.addEventListener('load', () => {

                    this.form.attaches.push({
                        file: reader.result,
                        filename: file.name
                    });
                });

                reader.readAsDataURL(file);
            },

            removeAttach: function (index) {
                this.form.attaches.splice(index, 1);
            }
        }
    }
</script>
