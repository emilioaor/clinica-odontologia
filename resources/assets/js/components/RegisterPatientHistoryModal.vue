<template>
    <div class="modal fade" :id="modalId" tabindex="-1" role="dialog" :aria-labelledby="modalId + 'Label'" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="product">Servicio</label>
                                <select
                                        name="product"
                                        id="product"
                                        class="form-control"
                                        v-model="data.product_id"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('product')}"
                                        >
                                    <option
                                            v-for="product in products"
                                            :value="product.id"
                                            >
                                        {{ product.name }}
                                    </option>
                                </select>
                                <p class="error" v-if="errors.firstByRule('product', 'required')">
                                    Requerido
                                </p>
                            </div>
                        </div>

                        <div class="col-xs-6">
                            <div class="form-group">
                                <label for="assistant">Asistente</label>
                                <select
                                        name="assistant"
                                        id="assistant"
                                        class="form-control"
                                        v-model="data.assistant_id"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('assistant')}"
                                        >
                                    <option
                                            v-for="assistant in assistants"
                                            :value="assistant.id"
                                            >
                                        {{ assistant.name }}
                                    </option>
                                </select>
                                <p class="error" v-if="errors.firstByRule('assistant', 'required')">
                                    Requerido
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-4">
                            <div class="form-group">
                                <label for="qty">Qty</label>
                                <input
                                        type="number"
                                        id="qty"
                                        name="qty"
                                        class="form-control"
                                        v-model="data.qty"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('qty')}"
                                        >
                                <p class="error" v-if="errors.firstByRule('qty', 'required')">
                                    Requerido
                                </p>
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <div class="form-group">
                                <label for="tooth">Diente</label>
                                <input
                                        type="text"
                                        id="tooth"
                                        name="tooth"
                                        class="form-control"
                                        v-model="data.tooth"
                                        >
                            </div>
                        </div>

                        <div class="col-xs-4">
                            <div class="form-group">
                                <label for="unit_price">Precio</label>
                                <input
                                        type="number"
                                        id="unit_price"
                                        name="unit_price"
                                        class="form-control"
                                        v-model="data.unit_price"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('unit_price')}"
                                        >
                                <p class="error" v-if="errors.firstByRule('unit_price', 'required')">
                                    Requerido
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <p>
                                <strong>Total:</strong>
                                {{ getTotal() }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                            type="button"
                            class="btn btn-success"
                            @click="validate()"
                            v-if="! loading"
                            >
                        Registrar
                    </button>
                    <button
                            type="button"
                            :id="modalId + 'Close'"
                            class="btn btn-secondary"
                            data-dismiss="modal"
                            v-if="! loading"
                            >
                        Cerrar
                    </button>

                    <img src="/img/loading.gif" v-if="loading">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            modalId: {
                type: String,
                required: true
            },
            patientId: {
                type: Number,
                required: true
            }
        },

        data: function () {
            return {
                products: [],
                assistants: [],
                loading: false,
                data: {
                    product_id: null,
                    assistant_id: null,
                    patient_id: this.patientId,
                    qty: null,
                    tooth: null,
                    unit_price: null
                }
            }
        },

        mounted: function () {
            this.getProducts();
            this.getAssistants();
        },

        methods: {

            getProducts: function () {

                axios.get('/user/product/list')
                    .then((res) => {
                        if (res.data.success) {
                            this.products = res.data.products;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                    });
            },

            getAssistants: function () {
                axios.get('/user/user/assistant')
                    .then((res) => {
                        if (res.data.success) {
                            this.assistants = res.data.assistants;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                    });
            },

            validate: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.register();
                    }
                });
            },

            register: function () {
                this.loading = true;

                axios.post('/user/service/register/patientHistory', this.data)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {

                            $('#' +  this.modalId + 'Close').click();

                            this.data.product_id = null;
                            this.data.assistant_id = null;
                            this.data.patient_id = this.patientId;
                            this.data.qty = null;
                            this.data.tooth = null;
                            this.data.unit_price = null;

                            this.$emit('register-patient-history');
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.loading = false;
                    });
            },

            getTotal: function () {
                if (
                        this.data.qty === null ||
                        this.data.unit_price === null ||
                        parseInt(this.data.qty) != this.data.qty ||
                        parseInt(this.data.unit_price) != this.data.unit_price
                ) {
                    return 0;
                }

                return this.data.qty * this.data.unit_price;
            }
        }
    }
</script>