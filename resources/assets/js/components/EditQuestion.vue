<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Responder pregunta
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
                                        <label>De</label>
                                        <p>{{ form.from.name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Para</label>
                                        <p>{{ form.to.name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Titulo</label>
                                        <p>{{ form.title }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Pregunta</label>
                                        <p v-html="form.question"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="form.question_attaches.length">
                                <div class="col-xs-12">
                                    <label>Documentos adjuntos</label>
                                </div>

                                <div class="col-sm-3" v-for="(attach, index) in form.question_attaches">
                                    <div class="form-group">
                                        <a :href="'/' + attach.url" download="">
                                            {{ attach.filename.length <= 15 ? attach.filename : attach.filename.substring(attach.filename.length - 15) }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="form.answered">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>Respuesta</label>
                                        <p v-html="form.answer"></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="user.id === form.to.id && ! form.answered">
                                <div class="col-xs-12">
                                    <div class="form-group" v-bind:class="{'has-error': errors.has('answer')}">
                                        <label for="answer">Respuesta</label>
                                        <textarea
                                                cols="30"
                                                rows="8"
                                                id="answer"
                                                name="answer"
                                                class="form-control"
                                                v-validate
                                                data-vv-rules="required"
                                                v-model="form.answer"
                                                v-bind:class="{'input-error': errors.has('answer')}"
                                                ></textarea>
                                        <p class="error" v-if="errors.firstByRule('answer', 'required')">
                                            Este campo es requerido
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="user.id === form.to.id && ! form.answered">
                                <div class="col-xs-12">
                                    <button class="btn btn-success" v-if="!loading">
                                        <i class="glyphicon glyphicon-send"></i>
                                        Responder
                                    </button>

                                    <img src="/img/loading.gif" v-if="loading">
                                </div>
                            </div>

                            <div class="row" v-if="user.id === form.from.id">
                                <div class="col-xs-12">
                                    <button type="button" class="btn btn-warning" v-if="!loading && !form.hide" @click="hideQuestion()">
                                        <i class="glyphicon glyphicon-eye-close"></i>
                                        Ocultar
                                    </button>

                                    <button
                                            type="button"
                                            class="btn btn-danger"
                                            data-toggle="modal"
                                            data-target="#deleteModal"
                                            >
                                        <i class="glyphicon glyphicon-remove"></i>
                                        Eliminar
                                    </button>

                                    <img src="/img/loading.gif" v-if="loading">
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>¿Esta seguro de eliminar esta pregunta?</h4>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-show="! loading"
                                id="closeDeleteModal">
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                @click="sendDelete()"
                                v-show="! loading">
                            SI
                        </button>

                        <img src="/img/loading.gif" v-if="loading">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            viewData: {
                required: true,
                type: Object
            },
            user: {
                required: true,
                type: Object
            }
        },
        data: function () {
            return {
                loading: false,
                form: this.viewData
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

                axios.put('/user/question/' + this.form.public_id, this.form)
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

            hideQuestion: function () {
                this.loading = true;

                axios.put('/user/question/' + this.form.public_id + '/hide')
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

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/question/' + this.form.public_id)
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
                        console.log(err);
                    })
            }
        }
    }
</script>
