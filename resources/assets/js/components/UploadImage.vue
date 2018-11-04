<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    Carga basica de imagenes
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-8">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <p>
                            <strong>Paciente:</strong>
                            {{ patient.name }}
                        </p>

                        <div class="btn-space">
                            <button class="btn btn-danger btn-xs" @click="removeImage">
                                <i class="glyphicon glyphicon-minus"></i>
                            </button>

                            <button class="btn btn-success btn-xs" @click="addImage">
                                <i class="glyphicon glyphicon-plus"></i>
                            </button>
                        </div>

                        <form @submit="validateForm" method="post" enctype="multipart/form-data">

                            <input type="hidden" name="_token" :value="token">

                            <div class="form-group" v-for="i in images">

                                <input
                                        type="file"
                                        class="form-control"
                                        :name="'image' + i"
                                        :class="{'input-error': errors.has('image' + i)}"
                                        v-validate
                                        data-vv-rules="required|mimes:image/jpeg,image/png|size:5120"
                                        >

                                <p class="error" v-if="errors.firstByRule('image' + i, 'required')">
                                    Requerido
                                </p>
                                <p class="error" v-if="errors.firstByRule('image' + i, 'size')">
                                    Maximo 5 mb
                                </p>
                                <p class="error" v-if="errors.firstByRule('image' + i, 'mimes')">
                                    Imagen necesita ser formato .png o .jpg
                                </p>

                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary">
                                    <i class="glyphicon glyphicon-upload"></i>
                                    Cargar
                                </button>
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
            patient: {
                required: true,
                type: Object
            }
        },
        data: function () {
            return {
                images: 1,
                token: null
            }
        },

        mounted: function () {
            this.token = document.head.querySelector('meta[name="csrf-token"]').content;
        },

        methods: {
            validateForm: function (e) {

                this.$validator.validateAll().then((res) => {
                    if (! res) {
                        e.preventDefault();
                    }
                });
            },

            addImage: function () {
                this.images++;
            },

            removeImage: function () {
                if (this.images > 1) {
                    this.images--;
                }
            }
        }
    }
</script>

<style scoped>
    p {
        font-size: 15px;
    }
    .btn-space {
        position: absolute;
        top: 0;
        right: 0;
        padding: 10px 35px;
    }
</style>