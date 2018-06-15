<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-envelope" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Configurar email
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row" v-for="(product, index) in products">
                            <div class="col-xs-12">
                                <p>
                                    <a @click="changeProduct(product)">
                                        {{ product.name }}
                                    </a>
                                </p>

                                <div v-if="selectedProduct === product.id">
                                    <div class="form-group">
                                        <label for="send_email">¿Enviar correo al registrar servicio?</label>
                                        <input type="checkbox" id="send_email" v-model="product.send_email">
                                    </div>

                                    <div class="form-group">
                                        <label for="email_title">Asunto</label>
                                        <input
                                                type="text"
                                                name="email_title"
                                                id="email_title"
                                                class="form-control"
                                                :class="{'input-error': errors.has('email_title')}"
                                                placeholder="Asunto"
                                                v-model="product.email_title"
                                                v-validate
                                                data-vv-rules="required"
                                            >
                                        <p class="error" v-if="errors.firstByRule('email_title', 'required')">
                                            Requerido
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label for="email_text">Email</label>
                                        <textarea
                                                name="email_text"
                                                id="email_text"
                                                class="form-control"
                                                :class="{'input-error': errors.has('email_text')}"
                                                rows="6"
                                                placeholder="Email"
                                                v-model="product.email_text"
                                                v-validate
                                                data-vv-rules="required"
                                                ></textarea>

                                        <p class="error" v-if="errors.firstByRule('email_text', 'required')">
                                            Requerido
                                        </p>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-3" v-for="(attach, i) in attaches">
                                            <div class="form-group">
                                                <a @click="removeAttach(index, i)" class="text-danger">X</a>
                                                {{ attach.filename.length <= 15 ? attach.filename : attach.filename.substring(attach.filename.length - 15) }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <input type="file" id="attach" class="hide" @change="setAttach(index)">
                                                <a onclick="$('#attach').click()">
                                                    <i class="glyphicon glyphicon-paperclip"></i>
                                                    Adjuntar
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button
                                                class="btn btn-success"
                                                @click="validateForm(product)"
                                                v-if="!loading"
                                            >
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Guardar
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            products: {
                type: Array,
                required: true
            }
        },
        data: function () {
            return {
                loading: false,
                selectedProduct: null,
                attaches: []
            }
        },

        methods: {
            validateForm: function (product) {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm(product);
                    }
                });
            },

            sendForm: function (product) {
                this.loading = true;

                axios.put('/user/email/' + product.public_id, product)
                    .then((res) => {
                        location.reload();
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                    });
            },

            changeProduct: function (product) {
                this.selectedProduct = this.selectedProduct && this.selectedProduct === product.id ? null : product.id;
                this.attaches = product.email_attaches;
            },

            setAttach: function(index) {
                const file = $('#attach')[0].files[0];

                const reader = new FileReader();

                reader.addEventListener('load', () => {

                    this.attaches.push({
                        file: reader.result,
                        filename: file.name
                    });

                    this.products[index].email_attaches = this.attaches;
                });

                reader.readAsDataURL(file);
            },

            removeAttach: function (p, a) {
                this.attaches.splice(a, 1);
                this.products[p].email_attaches = this.attaches;
            }
        }
    }
</script>
