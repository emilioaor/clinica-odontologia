<template>
    <section class="register-budget">
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Generar cotización
                    </h1>
                </div>
            </div>
            <div class="row">
                <!-- Cotizador -->
                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-body register-budget__header">

                            <!-- Header -->
                            <div class="row">
                                <!-- Left -->
                                <div class="col-xs-5">
                                    <div class="form-group">
                                        <h3>{{ form.business_name }}</h3>
                                    </div>

                                    <div class="form-group">
                                        <input
                                                type="text"
                                                class="form-control input-hover"
                                                id="client_label"
                                                name="client_label"
                                                placeholder="Cotizado a"
                                                v-model="form.client_label"
                                                >
                                        <input
                                                type="text"
                                                class="form-control"
                                                id="client_value"
                                                name="client_value"
                                                placeholder="Nombre cliente"
                                                v-model="form.client_value"
                                                >
                                    </div>
                                </div>

                                <!-- Right -->
                                <div class="col-xs-5 col-xs-offset-2">
                                    <div class="form-group">
                                        <input
                                                type="text"
                                                class="form-control input-hover"
                                                id="title"
                                                name="title"
                                                placeholder="Titulo"
                                                v-model="form.title"
                                                >
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-xs-8 col-xs-offset-4">
                                                <div class="input-group">
                                                    <span class="input-group-addon" id="basic-addon1">#</span>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            id="public_id"
                                                            name="public_id"
                                                            placeholder="Número"
                                                            aria-describedby="basic-addon1"
                                                            v-model="form.public_id"
                                                            >
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <section class="register-budget__dates">
                                        <div class="form-group">
                                            <div class="row">
                                                <article class="col-xs-6">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="creation_date_label"
                                                            name="creation_date_label"
                                                            v-model="form.creation_date_label"
                                                            >
                                                </article>

                                                <article class="col-xs-6">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            id="creation_date_value"
                                                            name="creation_date_value"
                                                            v-model="form.creation_date_value"
                                                            >
                                                </article>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <article class="col-xs-6">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="expiration_date_label"
                                                            name="expiration_date_label"
                                                            v-model="form.expiration_date_label"
                                                            >
                                                </article>

                                                <article class="col-xs-6">
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            id="expiration_date_value"
                                                            name="expiration_date_value"
                                                            v-model="form.expiration_date_value"
                                                            >
                                                </article>
                                            </div>
                                        </div>

                                        <div class="form-group total">
                                            <div class="row">
                                                <article class="col-xs-6">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="total_head_label"
                                                            name="total_head_label"
                                                            v-model="form.total_head_label"
                                                            >
                                                </article>

                                                <article class="col-xs-6">
                                                    <p>{{ currencySymbol + ' ' + getTotal() }}</p>
                                                </article>
                                            </div>
                                        </div>
                                    </section>

                                </div>
                            </div>
                            <!-- /Header -->

                            <!-- Table -->
                            <div class="row">
                                <div class="col-xs-12 register-budget__table">
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th width="55%">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="table_description_label"
                                                            name="table_description_label"
                                                            v-model="form.table_description_label"
                                                            >
                                                </th>
                                                <th width="14%">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="table_quantity_label"
                                                            name="table_quantity_label"
                                                            v-model="form.table_quantity_label"
                                                            >
                                                </th>
                                                <th width="14%">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="table_price_label"
                                                            name="table_price_label"
                                                            v-model="form.table_price_label"
                                                            >
                                                </th>
                                                <th width="14%">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="table_total_label"
                                                            name="table_total_label"
                                                            v-model="form.table_total_label"
                                                            >
                                                </th>
                                                <th width="3%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="detail,id in form.details">
                                                <td>
                                                    <select
                                                            class="form-control"
                                                            v-model="detail.product_id"
                                                            v-bind:name="'detail' + id + '.product_id'"
                                                            @change="changeProduct(detail)"
                                                            >
                                                        <option value="null">- Selecciona un producto</option>
                                                        <option
                                                            v-for="product in productList"
                                                            v-bind:value="product.id"
                                                            >
                                                            {{ product.name }}
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input
                                                            type="number"
                                                            class="form-control"
                                                            v-model="detail.quantity"
                                                            >
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            v-model="detail.price"
                                                            disabled
                                                            >
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            v-bind:value="currencySymbol + ' ' + (detail.price * detail.quantity)"
                                                            disabled
                                                            >

                                                </td>
                                                <td>
                                                    <a @click="removeDetail(id)">X</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5">
                                                    <button
                                                            class="btn btn-success"
                                                            @click="addDetail()"
                                                            v-bind:disabled="form.details.length >= productList.length"
                                                            >
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        Agregar detalle
                                                    </button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <!-- /Table -->

                            <!-- Footer -->
                            <div class="row">
                                <div class="col-xs-12 register-budget__footer">

                                    <div class="row">
                                        <div class="col-xs-5 col-xs-offset-7">

                                            <div class="form-group">
                                                <div class="row">
                                                    <article class="col-xs-6">
                                                        <input
                                                                type="text"
                                                                class="form-control input-hover"
                                                                id="total_footer_label"
                                                                name="total_footer_label"
                                                                v-model="form.total_footer_label"
                                                                >
                                                    </article>

                                                    <article class="col-xs-6">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                id="total_footer_value"
                                                                name="total_footer_value"
                                                                v-bind:value="currencySymbol + ' ' + getSubTotal()"
                                                                disabled
                                                                >
                                                    </article>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /Footer -->

                            <div class="form-group">
                                <div class="text-center">
                                    <button class="btn btn-primary btn-lg" @click="sendForm()">
                                        <i class="glyphicon glyphicon-save"></i>
                                        Generar cotización
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Controls -->
                <div class="col-md-2">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <div class="form-group">
                                <h5>Moneda</h5>
                                <input type="text" class="form-control" v-model="currencySymbol">
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
        props: ['products'],

        data: function () {
            return {
                loading: false,
                currencySymbol: 'VEF',
                productList: JSON.parse(this.products),
                form: {
                    public_id: '',
                    business_name: 'Mi empresa',
                    business_logo: '',
                    title: 'COTIZACIÓN',
                    client_label: 'Cotizado a:',
                    client_value: 'Cliente',
                    creation_date_label: 'Fecha de emisión',
                    creation_date_value: '',
                    expiration_date_label: 'Fecha de expiración',
                    expiration_date_value: '',
                    total_head_label: 'Total:',
                    total_head_value: 0,
                    discount_label: '',
                    discount_type: '',
                    discount_value: '',
                    tax_label: '',
                    tax_type: '',
                    tax_value: '',
                    shaping_label: '',
                    shaping_value: '',
                    subtotal_footer_label: 'Subtotal',
                    subtotal_footer_value: '',
                    total_footer_label: 'Total',
                    total_footer_value: '',
                    amount_paid_label: 'Monto total:',
                    amount_paid_value: '',
                    notes_label: 'Notas',
                    notes_value: '',
                    terms_label: 'Terminos y condiciones',
                    term_value: '',
                    table_description_label: 'Item',
                    table_quantity_label: 'Cant.',
                    table_price_label: 'Precio',
                    table_total_label: 'Total',
                    details: []
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

                console.log('sendForm', this.form);

                axios.post('/user/budget', this.form)
                    .then((res) => {
                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        this.loading = false;

                        console.log('Error', err);
                    })
                ;
            },

            addDetail: function() {
                this.form.details.push({
                    product_id: null,
                    quantity: 1,
                    price: 0,
                });
            },

            removeDetail: function (index) {
                this.form.details.splice(index, 1);
            },

            changeProduct: function (detail) {
                if (this.hasProduct(detail.product_id, 2)) {
                    detail.product_id = null;
                }

                for (let i in this.productList) {
                    if (this.productList[i].id === detail.product_id) {
                        return detail.price = this.productList[i].price;
                    }
                }

                detail.price = 0;
            },

            getSubTotal: function () {
                let total = 0;

                for (let i in this.form.details) {
                    total += this.form.details[i].price * this.form.details[i].quantity;
                }

                return total;
            },

            getTotal: function () {
                return this.getSubTotal();
            },

            hasProduct: function (productId, views) {
                let c = 0;

                for (let i in this.form.details) {
                    if (this.form.details[i].product_id === productId) {
                        c++;
                    }

                    if (c >= views) {
                        return true;
                    }
                }

                return false;
            }
        }
    }
</script>
