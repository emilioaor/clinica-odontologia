<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-list-alt"></i>
                    Lista de seguimientos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <table class="table table-responsive table-striped">
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Telefono</th>
                                <th>Email</th>
                                <th>Nota</th>
                                <th>Asignado a</th>
                                <th width="5%"></th>
                                <th width="5%"></th>
                            </tr>
                            </thead>
                            <tbody>
                                <template v-for="tracking in trackingList">
                                    <!-- Info -->
                                    <tr>
                                        <td>{{ tracking.name }}</td>
                                        <td>{{ tracking.phone }}</td>
                                        <td>{{ tracking.email }}</td>
                                        <td>{{ tracking.note }}</td>
                                        <td>
                                            {{ tracking.secretary ? tracking.secretary.name : '' }}
                                        </td>
                                        <td>
                                            <button
                                                    type="button"
                                                    class="btn btn-primary"
                                                    data-toggle="modal"
                                                    data-target="#changeStatusTrackingModal"
                                                    @click="selectTracking(tracking)"
                                            >
                                                <i class="glyphicon glyphicon-edit"></i>
                                            </button>
                                        </td>
                                        <td>
                                            <button
                                                    type="button"
                                                    class="btn btn-danger"
                                                    v-if="user.hasRole.admin"
                                                    data-toggle="modal"
                                                    data-target="#deleteTrackingModal"
                                                    @click="trackingToDelete = tracking.id"
                                            >
                                                <i class="glyphicon glyphicon-remove"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Notes -->
                                    <tr v-if="tracking.tracking_notes.length">
                                        <td colspan="7">
                                            <div class="alert alert-info" v-for="trackingNote in tracking.tracking_notes" :key="trackingNote.id">
                                                <p>
                                                    <strong>{{ trackingNote.user.name }}:</strong><br>
                                                    {{ trackingNote.note }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="changeStatusTrackingModal" tabindex="-1" role="dialog" aria-labelledby="changeStatusTrackingModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12" v-if="user.hasRole.admin">
                                <label for="status">Asignado a</label>
                                <select
                                        name="secretary"
                                        id="secretary"
                                        class="form-control"
                                        v-model="form.secretary_id"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('secretary')}"
                                >
                                    <option :value="0">- Sin asignar</option>
                                    <option
                                            v-for="secretary in secretaries"
                                            :key="secretary.id"
                                            :value="secretary.id"
                                    >
                                        {{ secretary.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-xs-12">
                                <label for="status">Estatus</label>
                                <select
                                        name="status"
                                        id="status"
                                        class="form-control"
                                        v-model="form.status"
                                >
                                    <option :value="0">Pendiente</option>
                                    <option :value="1">Resuelto</option>
                                </select>
                            </div>
                            
                            <div class="col-xs-12">
                                <label for="note">Nota</label>
                                <textarea
                                        name="note"
                                        id="note"
                                        cols="30"
                                        rows="4"
                                        class="form-control"
                                        v-model="form.note"
                                        v-validate
                                        :class="{'input-error': errors.has('note')}"
                                ></textarea>
                                <p class="error" v-if="errors.firstByRule('note', 'required')">
                                    Campo requerido
                                </p>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <img src="/img/loading.gif" v-if="loading">

                        <button type="button" class="btn btn-success" @click="validateForm()" v-if="! loading">
                            Guardar
                        </button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" v-if="! loading">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteTrackingModal" tabindex="-1" role="dialog" aria-labelledby="deleteTrackingModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>¿Esta seguro de eliminar este seguimiento?</h4>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-if="! loading">
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                @click="deleteTracking()"
                                v-if="! loading">
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
            trackingList: {
                type: Array,
                required: true
            },

            secretaries: {
                type: Array,
                required: true
            },

            user: {
                type: Object,
                required: true
            }
        },

        data () {
            return {
                loading: false,
                form: {
                    note: null,
                    tracking_id: null,
                    status: null,
                    secretary_id: 0
                },
                trackingToDelete: null
            }
        },

        methods: {
            validateForm () {
                this.$validator.validateAll().then(res => {
                    if (res) {
                        this.sendForm()
                    }
                })
            },

            sendForm () {
                this.loading = true;

                axios.post('/user/tracking/note', this.form)
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
                    })
                ;
            },

            deleteTracking () {
                this.loading = true;

                axios.delete('/user/tracking/' + this.trackingToDelete)
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
                    })
                ;
            },

            selectTracking (tracking) {
                this.form.tracking_id = tracking.id;
                this.form.status = tracking.status;
                this.form.secretary_id = tracking.secretary_id ? tracking.secretary_id : 0;
            }
        }
    }
</script>