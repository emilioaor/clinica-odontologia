<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-cog" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Configurar comisiones
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
                                        data-target="#doctorModal"
                                        @click="searchDoctor()"
                                        >
                                    <i class="glyphicon glyphicon-search"></i>

                                    <span v-if="! doctor">
                                        Seleccionar doctor
                                    </span>
                                    <span v-if="doctor">
                                        Cambiar doctor
                                    </span>
                                </a>
                                <hr>
                            </div>
                        </div>

                        <section v-if="doctor">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <p>
                                            {{ doctor.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Telefono</label>
                                        <p>
                                            {{ doctor.phone }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <p>
                                            {{ doctor.email }}
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xs-12">

                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th>Precio</th>
                                                <th width="20%">Comisión</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(product, index) in doctor.commission_products">
                                                <td>{{ product.public_id }}</td>
                                                <td>{{ product.name }}</td>
                                                <td>{{ product.price }}</td>
                                                <td>
                                                    <input  type="number"
                                                            :id="'commission' + index"
                                                            :name="'commission' + index"
                                                            v-model="product.pivot.commission"
                                                            class="form-control"
                                                            placeholder="Comisión"
                                                            :class="{'input-error': errors.has('commission' + index)}"
                                                            min="1"
                                                            v-validate
                                                            data-vv-rules="required|numeric"
                                                        >
                                                    <p class="error" v-if="errors.firstByRule('commission' + index, 'required')">
                                                        Campo requerido
                                                    </p>
                                                    <p class="error" v-if="errors.firstByRule('commission' + index, 'numeric')">
                                                        Formato invalido
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <button
                                                class="btn btn-success"
                                                @click="validateForm()"
                                                v-if="!loading"
                                                >
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Actualizar
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">
                                    </div>
                                </div>
                            </div>

                        </section>

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
                                    <tr v-for="d in modal.data">
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
    </section>
</template>

<script>
    export default {
        data: function () {
          return {
              loading: false,
              doctor: null,
              modal: {
                  data: [],
                  loading: false,
                  search: ''
              },
          }
        },
        methods: {
            searchDoctor: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/admin/user/search?level=doctor&search=' + this.modal.search)
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
            },

            validateForm: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/admin/commission/' + this.doctor.public_id, this.doctor)
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
                ;
            }
        }
    }
</script>