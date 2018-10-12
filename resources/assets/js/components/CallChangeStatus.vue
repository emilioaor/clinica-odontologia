<template>
    <div class="modal fade" id="callModal" tabindex="-1" role="dialog" aria-labelledby="callModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="status">Estatus</label>
                        <select
                                name="status"
                                id="status"
                                class="form-control"
                                v-model="form.status"
                                @focus="hasCancelAppointment()"
                                v-validate
                                data-vv-rules="required"
                                :class="{'input-error': errors.has('status')}"
                            >
                            <option
                                    value="2"
                                    :disabled="disabledAppointment"
                                    :class="{'bg-danger': disabledAppointment}"
                                    >
                                Con cita
                            </option>
                            <option value="3">
                                No interesado
                            </option>
                            <option value="4">
                                No contesto
                            </option>
                            <option value="5">
                                Volver a llamar
                            </option>
                        </select>
                        <p class="error" v-if="errors.firstByRule('status', 'required')">
                            Campo requerido
                        </p>
                    </div>

                    <div class="form-group" v-if="form.status == 5 || form.status == 2">
                        <label for="date_again">Fecha</label>
                        <datepicker
                                name = "date_again"
                                id = "date_again"
                                language="es"
                                input-class = "form-control"
                                format = "MM/dd/yyyy"
                                @input="changeDate($event)"
                                v-model="initDate"
                                ></datepicker>
                    </div>

                    <div class="form-group">
                        <label for="note">Nota</label>
                                <textarea
                                        name="note"
                                        id="note"
                                        class="form-control"
                                        placeholder="Notas de la llamada"
                                        rows="4"
                                        v-model="form.note"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('note')}"
                                        ></textarea>
                        <p class="error" v-if="errors.firstByRule('note', 'required')">
                            Campo requerido
                        </p>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" v-if="!loading">
                        Cerrar
                    </button>
                    <button type="button" class="btn btn-primary" @click="validateForm()" v-if="!loading">
                        Guardar
                    </button>

                    <img src="/img/loading.gif" v-if="loading">
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        components: {
            Datepicker
        },
        data: function () {
            return {
                initDate: new Date(),
                loading: false,
                form: {
                    note: '',
                    status: null,
                    date_again: null
                },
                disabledAppointment: false
            }
        },

        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.form.date_again = year + '-' + month + '-' + day;
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

                axios.put('/user/callLog/' + window.callLogPublicId, this.form)
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

            changeDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.form.date_again = year + '-' + month + '-' + day;
            },

            hasCancelAppointment: function () {
                this.disabledAppointment = (window.cancelAppointment === 1);
            }
        }
    }
</script>