<template>
    <div class="modal fade" :id="'supplyRequestModal' + public_id" tabindex="-1" role="dialog" aria-labelledby="callModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">

                    <h3>
                        <strong>Solicitud:</strong>
                        {{ public_id }}
                    </h3>

                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th width="50%">Insumo</th>
                                <th width="25%">Qty</th>
                                <th width="25%">Aprobado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(detail, id) in this.form.details">
                                <td>{{ detail.supply.name }}</td>
                                <td>{{ detail.qty }}</td>
                                <td>
                                    <input
                                            type="number"
                                            class="form-control"
                                            :name="'detail' + id"
                                            :class="{'input-error': errors.has('detail' + id)}"
                                            v-model="detail.approved"
                                            v-validate
                                            data-vv-rules="required|min_value:1"
                                        >
                                    <p class="error" v-if="errors.firstByRule('detail' + id, 'required')">
                                        Campo requerido
                                    </p>
                                    <p class="error" v-if="errors.firstByRule('detail' + id, 'min_value')">
                                        Minimo 1
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-if="!loading">
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" @click="validateForm()" v-if="!loading">
                        Aprobar
                    </button>

                    <img src="/img/loading.gif" v-if="loading">
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        props: ['publicId', 'details'],
        data: function () {
            return {
                loading: false,
                public_id: this.publicId,
                form: {
                    details: JSON.parse(this.details)
                }
            }
        },

        methods: {

            validateForm: function () {
                this.$validator.validateAll().then((res) => {

                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.put('/user/supplyRequest/' + this.public_id, this.form)
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
            },
        }
    }
</script>