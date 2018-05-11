<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Registrar gastos
                    </h1>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <div class="row">
                                <div class="col-xs-12">
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th width="20%">Fecha</th>
                                            <th width="20%">Paciente</th>
                                            <th width="20%">Proveedor</th>
                                            <th width="40%">Gasto</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(expense, id) in expenses">
                                            <td>
                                                <datepicker
                                                        name = "end"
                                                        id = "end"
                                                        language="es"
                                                        input-class = "form-control datepicker"
                                                        format = "MM/dd/yyyy"
                                                        @input="changeDate($event, id)"
                                                        v-model="expense.picker"
                                                        ></datepicker>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'patient' + id"
                                                        :id="'patient' + id"
                                                        class="form-control"
                                                        placeholder="Paciente"
                                                        v-model="expense.patient_id"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('patient' + id)}"
                                                        >
                                                    <option
                                                            v-for="patient in patients"
                                                            :value="patient.id"
                                                            >
                                                        {{ patient.name }}
                                                    </option>
                                                </select>

                                                <p class="error" v-if="errors.firstByRule('patient' + id, 'required')">
                                                    Requerido
                                                </p>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'supplier' + id"
                                                        :id="'supplier' + id"
                                                        class="form-control"
                                                        placeholder="Proveedor"
                                                        v-model="expense.supplier_id"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('supplier' + id)}"
                                                        >
                                                    <option
                                                            v-for="supplier in suppliers"
                                                            :value="supplier.id"
                                                            >
                                                        {{ supplier.name }}
                                                    </option>
                                                </select>

                                                <p class="error" v-if="errors.firstByRule('supplier' + id, 'required')">
                                                    Requerido
                                                </p>
                                            </td>
                                            <td>
                                                <input  type="text"
                                                        class="form-control"
                                                        placeholder="Descripción"
                                                        :name="'description' + id"
                                                        :id="'description' + id"
                                                        maxlength="255"
                                                        v-model="expense.description"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('description' + id)}"
                                                        >

                                                <p class="error" v-if="errors.firstByRule('description' + id, 'required')">
                                                    Requerido
                                                </p>
                                            </td>
                                            <td class="text-center">
                                                <a @click="remove()">X</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="5">
                                                <button class="btn btn-success" @click="add()">
                                                    <i class="glyphicon glyphicon-plus"></i>
                                                    Agregar
                                                </button>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <button class="btn btn-primary btn-lg"
                                            @click="validate()"
                                            v-if="!loading"
                                            >
                                        <i class="glyphicon glyphicon-check"></i>
                                        Guardar
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
        components: {
            Datepicker
        },
        props: {
            patients: {
                type: Array,
                required: true
            },
            suppliers: {
                type: Array,
                required: true
            }
        },
        data: function () {
            return {
                loading: false,
                expenses: [{
                    patient_id: null,
                    supplier_id: null,
                    description: null,
                    date: null,
                    picker: null
                }]
            }
        },

        mounted: function () {
            this.expenses[0].picker = new Date();
            this.changeDate(this.expenses[0].picker, 0);
        },

        methods: {
            changeDate: function (date, index) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.expenses[index].date = year + '-' + month + '-' + day;
            },

            add: function () {
                const index = this.expenses.length;

                this.expenses.push({
                    patient_id: null,
                    supplier_id: null,
                    description: null,
                    date: null,
                    picker: null
                });

                this.expenses[index].picker = new Date();
                this.changeDate(this.expenses[index].picker, index);
            },

            remove: function (index) {
                if (this.expenses.length > 1) {
                    this.expenses.splice(index, 1);
                }
            },

            validate: function () {
                this.$validator.validateAll().then((res) => {
                    if (res) {
                        this.sendForm();
                    }
                });
            },

            sendForm: function () {
                this.loading = true;

                axios.post('/user/expense', this.expenses)
                    .then((res) => {

                        if (res.data.success) {
                            location.href = res.data.redirect;
                        }
                    })
                    .catch((err) => {
                        console.log(err);
                        this.loading = false;
                    })
                ;
            }
        }
    }
</script>