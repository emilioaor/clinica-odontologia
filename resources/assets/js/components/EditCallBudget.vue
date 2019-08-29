<template>
<div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-edit"></i>
                    Editar envío de presupuesto
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="phone">Teléfono</label>
                                    <input 
                                        type="text" 
                                        class="form-control"
                                        placeholder="Teléfono"
                                        name="phone"
                                        id="phone"
                                        v-validate
                                        data-vv-rules="required|regex:^[0-9]{10}$"
                                        :class="{'input-error': errors.has('phone')}"
                                        maxlength="10"
                                        v-model="form.phone"
                                    >
                                    <span class="error" v-if="errors.firstByRule('phone', 'required')">
                                        Requerido
                                    </span>
                                    <span class="error" v-if="errors.firstByRule('phone', 'regex')">
                                        Formato invalido
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input 
                                        type="email" 
                                        class="form-control"
                                        placeholder="Email"
                                        name="email"
                                        id="email"
                                        v-validate
                                        data-vv-rules="required|email"
                                        :class="{'input-error': errors.has('email')}"
                                        maxlength="255"
                                        v-model="form.email"
                                    >
                                    <span class="error" v-if="errors.firstByRule('email', 'required')">
                                        Requerido
                                    </span>
                                    <span class="error" v-if="errors.firstByRule('email', 'email')">
                                        Formato invalido
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="name">Nombre</label>
                                    <input 
                                        type="text" 
                                        class="form-control"
                                        placeholder="Nombre"
                                        name="name"
                                        id="name"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('name')}"
                                        maxlength="255"
                                        v-model="form.name"
                                    >
                                    <span class="error" v-if="errors.firstByRule('name', 'required')">
                                        Requerido
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="service">Tratamiento</label>
                                    <input 
                                        type="text" 
                                        class="form-control"
                                        placeholder="Tratamiento"
                                        name="service"
                                        id="service"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('service')}"
                                        maxlength="255"
                                        v-model="form.service"
                                    >
                                    <span class="error" v-if="errors.firstByRule('service', 'required')">
                                        Requerido
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="amount">Monto estimado</label>
                                    <input 
                                        type="number" 
                                        class="form-control"
                                        placeholder="Estimado"
                                        name="amount"
                                        id="amount"
                                        v-validate
                                        data-vv-rules="required|regex:^[0-9]+$"
                                        :class="{'input-error': errors.has('amount')}"
                                        v-model="form.amount"
                                    >
                                    <span class="error" v-if="errors.firstByRule('amount', 'required')">
                                        Requerido
                                    </span>
                                    <span class="error" v-if="errors.firstByRule('amount', 'regex')">
                                        Formato invalido
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                        <label for="date">Ultima llamada</label>
                                        <datepicker
                                            name = "date"
                                            id = "date"
                                            language="es"
                                            input-class = "form-control"
                                            format = "MM/dd/yyyy"
                                            @input="changeDate($event)"
                                            v-model="initDate"
                                        ></datepicker>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="call_budget_source">Fuente</label>
                                    <select
                                        class="form-control"
                                        name="call_budget_source"
                                        id="call_budget_source"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('call_budget_source')}"
                                        v-model="form.call_budget_source_id"
                                    >
                                        <option
                                            v-for="(callBudgetSource, i) in callBudgetSources"
                                            :key="i"
                                            :value="callBudgetSource.id"
                                        >
                                            {{ callBudgetSource.name }}
                                        </option>
                                    </select>
                                    <span class="error" v-if="errors.firstByRule('call_budget_source', 'required')">
                                        Requerido
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="status">Estatus</label>
                                    <select
                                        class="form-control"
                                        name="status"
                                        id="status"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('status')}"
                                        v-model="form.status"
                                    >
                                        <option :value="1">Hizo cita</option>
                                        <option :value="2">Interesado</option>
                                        <option :value="3">No interesado</option>
                                        <option :value="4">Transferido</option>
                                    </select>
                                    <span class="error" v-if="errors.firstByRule('status', 'required')">
                                        Requerido
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="sell_manager">Vendedor</label>
                                    <select
                                            class="form-control"
                                            name="sell_manager"
                                            id="sell_manager"
                                            v-validate
                                            data-vv-rules="required"
                                            :class="{'input-error': errors.has('sell_manager')}"
                                            v-model="form.sell_manager_id"
                                    >
                                        <option :value="0" v-if="user.hasRole.admin">- Todos -</option>
                                        <option
                                                v-for="sellManager in sellManagers"
                                                :key="sellManager.id"
                                                :value="sellManager.id"
                                        >
                                            {{ sellManager.name }}
                                        </option>
                                    </select>
                                    <span class="error" v-if="errors.firstByRule('sell_manager', 'required')">
                                        Requerido
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row" v-if="form.status === 2">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="contact_type">Forma de contacto</label>
                                    <select
                                        class="form-control"
                                        name="contact_type"
                                        id="contact_type"
                                        v-validate
                                        data-vv-rules="required"
                                        :class="{'input-error': errors.has('contact_type')}"
                                        v-model="form.contact_type"
                                    >
                                        <option :value="1">Teléfono</option>
                                        <option :value="2">Email</option>
                                    </select>
                                    <span class="error" v-if="errors.firstByRule('contact_type', 'required')">
                                        Requerido
                                    </span>
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="contact_repeat">¿Días para volver a contactar?</label>
                                    <input
                                            type="number"
                                            class="form-control"
                                            placeholder="Días"
                                            name="contact_repeat"
                                            id="contact_repeat"
                                            v-validate
                                            data-vv-rules="required|regex:^[0-9]+$"
                                            :class="{'input-error': errors.has('contact_repeat')}"
                                            v-model="form.contact_repeat"
                                    >
                                    <span class="error" v-if="errors.firstByRule('contact_repeat', 'required')">
                                        Requerido
                                    </span>
                                    <span class="error" v-if="errors.firstByRule('contact_repeat', 'regex')">
                                        Formato invalido
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="notes">Notas</label>
                                    <textarea 
                                        class="form-control"
                                        placeholder="Notas"
                                        name="notes"
                                        id="notes"
                                        v-model="form.notes"
                                        rows="4"
                                    ></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <button class="btn btn-success" v-if="! loading" @click="validateForm()">
                                        <i class="glyphicon glyphicon-saved"></i>
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
    </div>
</template>

<script>
    import Datepicker from 'vuejs-datepicker';

    export default {
        components: {
            Datepicker
        },
        props: {
            callBudgetSources: {
                type: Array,
                required: true
            },
            callBudget: {
                type: Object,
                required: true
            },
            sellManagers: {
                type: Array,
                required: true
            },
            user: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                loading: false,
                initDate: new Date(),
                form: this.callBudget
            }
        },

        mounted() {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.form.date = year + '-' + month + '-' + day;
        },

        methods: {
            changeDate(date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.form.date = year + '-' + month + '-' + day;
            },

            validateForm() {
                this.$validator.validateAll().then(res => {
                    if (res) {
                        this.sendForm()
                    }
                })
            },

            sendForm() {
                this.loading = true;
                const data = {
                    ...this.form
                }

                axios.put('/user/callBudget/' + data.id, data)
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
            }
        }
    }
</script>
