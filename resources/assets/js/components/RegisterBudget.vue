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
                                        <div class="register-budget__logo" v-if="logo !== null && logo !== ''">
                                            <img v-bind:src="logo">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <p>
                                            {{ business_name }}
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <input
                                                type="text"
                                                class="form-control input-hover"
                                                id="client_label"
                                                name="client_label"
                                                v-model="form.client_label"
                                                >
                                        <input
                                                type="text"
                                                class="form-control"
                                                id="client_value"
                                                name="client_value"
                                                placeholder="Cliente"
                                                v-model="form.client_value"
                                                v-validate
                                                data-vv-rules="required"
                                                v-bind:class="{'input-error': errors.has('client_value')}"
                                                >
                                        <p class="error" v-if="errors.firstByRule('client_value', 'required')">
                                            Cliente es requerido
                                        </p>
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
                                                <h4 class="text-right">
                                                    #{{ form.public_id }}
                                                </h4>
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
                                                    <datepicker
                                                        name = "creation_date_value"
                                                        id = "creation_date_value"
                                                        language="es"
                                                        input-class = "form-control"
                                                        format = "MM/dd/yyyy"
                                                        v-model="initDate"
                                                        @input="setCreationDate($event)"
                                                    ></datepicker>
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
                                                    <p>{{ currencySymbol + getFinalTotal() + currencySymbol2 }}</p>
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
                                                <th width="51%">
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
                                                <th width="16%">
                                                    <input
                                                            type="text"
                                                            class="form-control input-hover"
                                                            id="table_price_label"
                                                            name="table_price_label"
                                                            v-model="form.table_price_label"
                                                            >
                                                </th>
                                                <th width="16%">
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
                                                            v-bind:name="'productId' + id"
                                                            @change="changeProduct(detail)"
                                                            v-validate
                                                            data-vv-rules="min_value:1"
                                                            v-bind:class="{'input-error': errors.has('productId' + id)}"
                                                            >
                                                        <option value="null">- Selecciona un producto</option>
                                                        <option
                                                            v-for="product in productList"
                                                            v-bind:value="product.id"
                                                            >
                                                            {{ product.name }}
                                                        </option>
                                                    </select>
                                                    <p class="error" v-if="errors.firstByRule('productId' + id, 'min_value')">
                                                        No olvide seleccionar el producto
                                                    </p>
                                                </td>
                                                <td>
                                                    <input
                                                            type="number"
                                                            class="form-control"
                                                            v-bind:name="'quantity' + id"
                                                            v-model="detail.quantity"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            min="1"
                                                            v-bind:class="{'input-error': errors.has('quantity' + id)}"
                                                            >
                                                    <p class="error" v-if="errors.firstByRule('quantity' + id, 'required')">
                                                        Precio
                                                    </p>
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            v-bind:value="currencySymbol + detail.price + currencySymbol2"
                                                            disabled
                                                            >
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            v-bind:value="currencySymbol + (detail.price * detail.quantity) + currencySymbol2"
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
                                                    <p class="error" v-if="form.details.length >= productList.length">
                                                        Ya agrego un detalle para cada producto registrado
                                                    </p>
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

                                    <!-- Subtotal -->
                                    <div class="row">
                                        <div class="col-xs-5 col-xs-offset-7">

                                            <div class="form-group">
                                                <div class="row">
                                                    <article class="col-xs-6">
                                                        <input
                                                                type="text"
                                                                class="form-control input-hover"
                                                                id="subtotal_footer_label"
                                                                name="subtotal_footer_label"
                                                                v-model="form.subtotal_footer_label"
                                                                >
                                                    </article>

                                                    <article class="col-xs-6">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                id="subtotal_footer_value"
                                                                name="subtotal_footer_value"
                                                                v-bind:value="currencySymbol + getSubTotal() + currencySymbol2"
                                                                disabled
                                                                >
                                                    </article>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Discount -->
                                    <div class="row" v-if="showDiscount">
                                        <div class="col-xs-5 col-xs-offset-7">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <div class="text-center">
                                                            <button class="btn btn-danger" @click="showDiscount = false; form.discount_value = null">
                                                                X
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <article class="col-xs-4">
                                                        <input
                                                                type="text"
                                                                class="form-control input-hover"
                                                                id="discount_label"
                                                                name="discount_label"
                                                                v-model="form.discount_label"
                                                                >
                                                    </article>

                                                    <article class="col-xs-3">
                                                        <input
                                                                type="number"
                                                                class="form-control"
                                                                id="discount_value"
                                                                name="discount_value"
                                                                v-model="form.discount_value"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                v-bind:class="{'input-error': errors.has('discount_value')}"
                                                                >
                                                        <p class="error" v-if="errors.firstByRule('discount_value', 'required')">
                                                            Requerido
                                                        </p>
                                                    </article>

                                                    <div class="col-xs-3">
                                                        <select
                                                                class="form-control"
                                                                v-model="form.discount_type"
                                                                >
                                                            <option value="1">%</option>
                                                            <option value="2">{{ currencySymbol !== '' ? currencySymbol : 'Monto' }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Tax -->
                                    <div class="row" v-if="showTax">
                                        <div class="col-xs-5 col-xs-offset-7">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <div class="text-center">
                                                            <button class="btn btn-danger" @click="showTax = false; form.tax_value = null">
                                                                X
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <article class="col-xs-4">
                                                        <input
                                                                type="text"
                                                                class="form-control input-hover"
                                                                id="tax_label"
                                                                name="tax_label"
                                                                v-model="form.tax_label"
                                                                >
                                                    </article>

                                                    <article class="col-xs-3">
                                                        <input
                                                                type="number"
                                                                class="form-control"
                                                                id="tax_value"
                                                                name="tax_value"
                                                                v-model="form.tax_value"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                v-bind:class="{'input-error': errors.has('tax_value')}"
                                                                >
                                                        <p class="error" v-if="errors.firstByRule('tax_value', 'required')">
                                                            Requerido
                                                        </p>
                                                    </article>

                                                    <div class="col-xs-3">
                                                        <select
                                                                class="form-control"
                                                                v-model="form.tax_type"
                                                                >
                                                            <option value="1">%</option>
                                                            <option value="2">{{ currencySymbol !== '' ? currencySymbol : 'Monto' }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Shipping -->
                                    <div class="row" v-if="showShipping">
                                        <div class="col-xs-5 col-xs-offset-7">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-2">
                                                        <div class="text-center">
                                                            <button class="btn btn-danger" @click="showShipping = false; form.shaping_value = null">
                                                                X
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <article class="col-xs-4">
                                                        <input
                                                                type="text"
                                                                class="form-control input-hover"
                                                                id="shaping_label"
                                                                name="shaping_label"
                                                                v-model="form.shaping_label"
                                                                >
                                                    </article>

                                                    <article class="col-xs-3">
                                                        <input
                                                                type="number"
                                                                class="form-control"
                                                                id="shaping_value"
                                                                name="shaping_value"
                                                                v-model="form.shaping_value"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                v-bind:class="{'input-error': errors.has('shaping_value')}"
                                                                >
                                                        <p class="error" v-if="errors.firstByRule('shaping_value', 'required')">
                                                            Requerido
                                                        </p>
                                                    </article>

                                                    <div class="col-xs-3">
                                                        <input
                                                                type="text"
                                                                class="form-control"
                                                                v-bind:value="currencySymbol"
                                                                disabled
                                                            >
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Select Tax, Discount, Shipping -->
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <p class="text-right">
                                                <a @click="showDiscount = true" v-if="!showDiscount">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Descuento
                                                </a>
                                                <a @click="showTax = true" v-if="!showTax">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Iva
                                                </a>
                                                <a @click="showShipping = true" v-if="!showShipping">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Envio
                                                </a>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Total -->
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
                                                                v-bind:value="currencySymbol + getTotal() + currencySymbol2"
                                                                disabled
                                                                >
                                                    </article>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Amount Paid -->
                                    <div class="row">
                                        <div class="col-xs-5 col-xs-offset-7">

                                            <div class="form-group">
                                                <div class="row">
                                                    <article class="col-xs-6">
                                                        <input
                                                                type="text"
                                                                class="form-control input-hover"
                                                                id="amount_paid_label"
                                                                name="amount_paid_label"
                                                                v-model="form.amount_paid_label"
                                                                >
                                                    </article>

                                                    <article class="col-xs-6">
                                                        <input
                                                                type="number"
                                                                class="form-control"
                                                                id="amount_paid_value"
                                                                name="amount_paid_value"
                                                                v-model="form.amount_paid_value"
                                                                >
                                                    </article>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        class="form-control input-hover"
                                                        id="notes_label"
                                                        name="notes_label"
                                                        v-model="form.notes_label"
                                                        >

                                                <textarea
                                                        name="notes_value"
                                                        id="notes_value"
                                                        placeholder="Todo lo relevante a la cotización"
                                                        class="form-control"
                                                        rows="2"
                                                        v-model="form.notes_value"
                                                        ></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Terms and conditions -->
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <div class="form-group">
                                                <input
                                                        type="text"
                                                        class="form-control input-hover"
                                                        id="terms_label"
                                                        name="terms_label"
                                                        v-model="form.terms_label"
                                                        >

                                                <textarea
                                                        name="terms_value"
                                                        id="terms_value"
                                                        placeholder="Intereses, metodos de pago, tiempos, etc"
                                                        class="form-control"
                                                        rows="2"
                                                        v-model="form.terms_value"
                                                        ></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- /Footer -->

                            <h4>This estimate is valid for 10 days</h4>

                            <div class="form-group">
                                <div class="text-center">
                                    <p class="error" v-if="errors.any()">
                                        Disculpe, hay algunos errores en la cotización
                                    </p>
                                    <button class="btn btn-primary btn-lg" @click="validateForm()" v-if="! loading">
                                        <i class="glyphicon glyphicon-save"></i>
                                        Generar cotización
                                    </button>
                                    <img src="/img/loading.gif" v-if="loading">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        props: ['products', 'userLogo', 'userBusinessName', 'next'],
        components: {
            Datepicker
        },

        data: function () {
            return {
                loading: false,
                currencySymbol: '$',
                currencySymbol2: ' USD',
                productList: JSON.parse(this.products),
                showTax: false,
                showDiscount: false,
                showShipping: false,
                logo: '/uploads/' + this.userLogo,
                business_name: this.userBusinessName,
                initDate: new Date(),
                form: {
                    public_id: this.next,
                    title: 'COTIZACIÓN',
                    client_label: 'Para:',
                    client_value: '',
                    creation_date_label: 'Fecha de emisión',
                    creation_date_value: '',
                    total_head_label: 'Total:',
                    total_head_value: null,
                    discount_label: 'Descuento',
                    discount_type: 1,
                    discount_value: null,
                    tax_label: 'Iva',
                    tax_type: 1,
                    tax_value: null,
                    shaping_label: 'Envio',
                    shaping_value: null,
                    subtotal_footer_label: 'Subtotal',
                    subtotal_footer_value: '',
                    total_footer_label: 'Total',
                    total_footer_value: '',
                    amount_paid_label: 'Monto pagado:',
                    amount_paid_value: null,
                    notes_label: 'Notas:',
                    notes_value: '',
                    terms_label: 'Terminos y condiciones:',
                    term_value: '',
                    table_description_label: 'Item',
                    table_quantity_label: 'Cant.',
                    table_price_label: 'Precio',
                    table_total_label: 'Total',
                    details: [{
                        price: 0,
                        quantity: 1,
                        product_id: null
                    }]
                }
            }
        },

        mounted: function () {
            const date = new Date();
            let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
            let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
            let year = date.getFullYear();

            this.form.creation_date_value = year + '-' + month + '-' + day;
            document.getElementById('creation_date_value').value = date;
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
                this.form.subtotal_footer_value = this.getSubTotal();
                this.form.total_footer_value = this.getTotal();
                this.form.total_head_value = this.getFinalTotal();

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
                if (this.form.details.length <= 1) {
                    return true;
                }

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
                let total = this.getSubTotal();

                // Decuento
                if (this.form.discount_value !== null && this.form.discount_value !== '') {
                    if (this.form.discount_type == 1) {
                        // Porcentaje
                        total -= (total * (this.form.discount_value / 100));
                    } else {
                        // Monto
                        total -= this.form.discount_value;
                    }
                }

                // Iva
                if (this.form.tax_value !== null && this.form.tax_value !== '') {
                    if (this.form.tax_type == 1) {
                        // Porcentaje
                        total += (total * (parseFloat(this.form.tax_value) / 100));
                    } else {
                        // Monto
                        total += parseFloat(this.form.tax_value);
                    }
                }

                // Envio
                if (this.form.shaping_value !== null && this.form.shaping_value !== '') {
                    total += parseFloat(this.form.shaping_value);
                }

                return total;
            },

            getFinalTotal: function () {
                let total = this.getTotal();

                if (this.form.amount_paid_value !== null && this.form.amount_paid_value !== '') {
                    total -= this.form.amount_paid_value;
                }

                return total;
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
            },

            setCreationDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.form.creation_date_value = year + '-' + month + '-' + day;
            },
        }
    }
</script>
