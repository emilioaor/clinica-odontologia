<template>
    <section class="container edit-service">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-search" v-if="! loading"></i>
                    <img src="/img/loading.gif" v-if="loading">
                    Busqueda de pagos
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-xs-12">
                                <a
                                        data-toggle="modal"
                                        data-target="#patientModal"
                                        @click="searchPatients()"
                                        >
                                    <i class="glyphicon glyphicon-search"></i>

                                    <span v-if="! patient">
                                        Seleccionar paciente
                                    </span>
                                    <span v-if="patient">
                                        Cambiar paciente
                                    </span>
                                </a>
                                <hr>
                            </div>
                        </div>

                        <section v-if="patient">

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Nombre</label>
                                        <p>
                                            {{ patient.name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Telefono</label>
                                        <p>
                                            {{ patient.phone }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Email</label>
                                        <p>
                                            {{ patient.email }}
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Servicios</label>
                                        <p>
                                            {{ '$ ' + getTotalServices() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Pagos</label>
                                        <p>
                                            {{ '$ ' + getTotalPayments() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Balance</label>
                                        <p>
                                            {{ '$ ' + getBalance() }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="">¿Filtrar?</label>
                                        <input
                                                type="checkbox"
                                                v-model="filter"
                                            >
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-show="filter">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Desde</label>
                                        <datepicker
                                                name = "start"
                                                id = "start"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changeStart($event)"
                                                v-model="initStart"
                                                ></datepicker>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="">Hasta</label>
                                        <datepicker
                                                name = "end"
                                                id = "end"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changeEnd($event)"
                                                v-model="initEnd"
                                                ></datepicker>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <button
                                                class="btn btn-primary"
                                                @click="search()"
                                                v-if="!loading"
                                                >
                                            <i class="glyphicon glyphicon-search"></i>
                                            Buscar
                                        </button>
                                        <img src="/img/loading.gif" v-if="loading">

                                        <button
                                                class="btn btn-success"
                                                type="button"
                                                data-toggle="modal"
                                                data-target="#paymentModal"
                                                >
                                            <i class="glyphicon glyphicon-usd"></i>
                                            Registrar pago
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="row" v-if="data.services.length || data.payments.length">
                                <div class="col-xs-12">
                                    <h4 class="bg-info text-info">Servicios</h4>
                                </div>

                                <div class="col-xs-12">

                                    <!-- Services -->
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>ID Servicio</th>
                                                <th>Servicio</th>
                                                <th>Diente</th>
                                                <th>Doctor</th>
                                                <th>Asistente</th>
                                                <th>Precio</th>
                                                <th width="5%" v-if="user.hasRole.admin"></th>
                                                <th width="5%" v-if="user.hasRole.admin"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(service, id) in data.services">
                                                <td>
                                                    <datepicker
                                                            :name = "'serviceDate' + id"
                                                            :id = "'serviceDate' + id"
                                                            language="es"
                                                            input-class = "form-control"
                                                            format = "MM/dd/yyyy"
                                                            @input="changeDateService($event, id)"
                                                            v-model="service.datePicker"
                                                            v-if="serviceEdit === service.id"
                                                            ></datepicker>

                                                    <span v-if="serviceEdit !== service.id">
                                                        {{ dateFormat(service.created_at) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ service.public_id }}
                                                </td>
                                                <td>
                                                    <select
                                                            :name="'product' + id"
                                                            :id="'product' + id"
                                                            placeholder="Servicio"
                                                            class="form-control"
                                                            v-model="service.product_id"
                                                            v-if="serviceEdit === service.id"
                                                        >
                                                        <option
                                                                v-for="product in products"
                                                                :value="product.id"
                                                            >
                                                            {{ product.name }}
                                                        </option>
                                                    </select>

                                                    <span v-if="serviceEdit !== service.id">
                                                        {{ service.product.name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                            type="text"
                                                            class="form-control"
                                                            :id="'tooth' + id"
                                                            :name="'tooth' + id"
                                                            placeholder="Diente"
                                                            v-model="service.tooth"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('tooth' + id)}"
                                                            v-if="serviceEdit === service.id"
                                                            >
                                                    <p class="error" v-if="errors.firstByRule('tooth' + id, 'required')">
                                                        Requerido
                                                    </p>

                                                    <span v-if="serviceEdit !== service.id">
                                                        {{ service.tooth }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <select
                                                            :name="'doctor' + id"
                                                            :id="'doctor' + id"
                                                            placeholder="Doctor"
                                                            class="form-control"
                                                            v-model="service.doctor_id"
                                                            v-if="serviceEdit === service.id"
                                                            >
                                                        <option
                                                                v-for="doctor in doctors"
                                                                :value="doctor.id"
                                                                >
                                                            {{ doctor.name }}
                                                        </option>
                                                    </select>

                                                    <span v-if="serviceEdit !== service.id">
                                                        {{ service.doctor.name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <select
                                                            :name="'assistant' + id"
                                                            :id="'assistant' + id"
                                                            placeholder="Asistente"
                                                            class="form-control"
                                                            v-model="service.assistant_id"
                                                            v-if="serviceEdit === service.id"
                                                            >
                                                        <option
                                                                v-for="assistant in assistants"
                                                                :value="assistant.id"
                                                                >
                                                            {{ assistant.name }}
                                                        </option>
                                                    </select>

                                                    <span v-if="serviceEdit !== service.id">
                                                        {{ service.assistant.name }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <input
                                                            type="number"
                                                            class="form-control"
                                                            :id="'price' + id"
                                                            :name="'price' + id"
                                                            placeholder="Precio"
                                                            v-model="service.price"
                                                            v-validate
                                                            data-vv-rules="required"
                                                            :class="{'input-error': errors.has('price' + id)}"
                                                            v-if="serviceEdit === service.id"
                                                        >
                                                    <p class="error" v-if="errors.firstByRule('price' + id, 'required')">
                                                        Requerido
                                                    </p>

                                                    <span v-if="serviceEdit !== service.id">
                                                        {{ '$' + service.price }}
                                                    </span>
                                                </td>
                                                <td v-if="user.hasRole.admin">
                                                    <!-- Cancelar edicion de servicio -->
                                                    <button
                                                            class="btn btn-warning"
                                                            @click="serviceEdit = null"
                                                            v-if="serviceEdit === service.id"
                                                            >
                                                        <i class="glyphicon glyphicon-remove-sign"></i>
                                                    </button>
                                                </td>
                                                <td v-if="user.hasRole.admin">
                                                    <!-- Editar servicio -->
                                                    <button
                                                            class="btn btn-warning"
                                                            @click="serviceEdit = service.id"
                                                            :disabled="serviceEdit !== null"
                                                            v-if="serviceEdit !== service.id"
                                                        >
                                                        <i class="glyphicon glyphicon-edit"></i>
                                                    </button>

                                                    <!-- Guardar servicio -->
                                                    <button
                                                            class="btn btn-success"
                                                            v-if="serviceEdit === service.id && serviceLoading !== service.id"
                                                            @click="updatePatientHistory(service)"
                                                            >
                                                        <i class="glyphicon glyphicon-check"></i>
                                                    </button>
                                                    <img src="/img/loading.gif" v-if="serviceLoading === service.id">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            <div class="row" v-if="data.services.length || data.payments.length">
                                <div class="col-xs-12">
                                    <h4 class="bg-info text-info">Pagos</h4>
                                </div>

                                <div class="col-xs-12">

                                    <!-- Payments -->
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Servicio</th>
                                            <th>Registrado por</th>
                                            <th>Tipo</th>
                                            <th>Monto</th>
                                            <th width="5%" v-if="user.hasRole.admin"></th>
                                            <th width="5%" v-if="user.hasRole.admin"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="(payment, id) in data.payments">
                                            <td>
                                                <datepicker
                                                        :name = "'paymentDate' + id"
                                                        :id = "'paymentDate' + id"
                                                        language="es"
                                                        input-class = "form-control"
                                                        format = "MM/dd/yyyy"
                                                        @input="changeDatePayment($event, id)"
                                                        v-model="payment.datePicker"
                                                        v-if="paymentEdit === payment.id"
                                                        :disabled="disabledDates"
                                                        ></datepicker>

                                                <span v-if="paymentEdit !== payment.id">
                                                    {{ dateFormat(payment.date) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ payment.patient_history ? payment.patient_history.product.name : '' }}
                                            </td>
                                            <td>
                                                <select
                                                        :name="'user_created' + id"
                                                        :id="'user_created' + id"
                                                        class="form-control"
                                                        placeholder="Secretaria"
                                                        v-model="payment.user_created_id"
                                                        v-if="paymentEdit === payment.id"
                                                    >
                                                    <option
                                                            v-for="secretary in secretaries"
                                                            :value="secretary.id"
                                                        >
                                                        {{ secretary.name }}
                                                    </option>
                                                </select>

                                                <span v-if="paymentEdit !== payment.id">
                                                    {{ payment.user_created.name }}
                                                </span>
                                            </td>
                                            <td>
                                                <select
                                                        :name="'type' + id"
                                                        :id="'type' + id"
                                                        class="form-control"
                                                        placeholder="Tipo de pago"
                                                        v-model="payment.type"
                                                        v-if="paymentEdit === payment.id"
                                                        >
                                                    <option value="1">Tarjeta de crédito</option>
                                                    <option value="2">Efectivo</option>
                                                    <option value="3">Cheque</option>
                                                    <option value="4">Descuento</option>
                                                </select>

                                                <span v-if="paymentEdit !== payment.id">
                                                    <span v-if="payment.type === 1">Tarjeta de crédito</span>
                                                    <span v-if="payment.type === 2">Efectivo</span>
                                                    <span v-if="payment.type === 3">Cheque</span>
                                                    <span v-if="payment.type === 4">Descuento</span>
                                                </span>
                                            </td>
                                            <td>
                                                <input
                                                        type="number"
                                                        class="form-control"
                                                        :id="'amount' + id"
                                                        :name="'amount' + id"
                                                        placeholder="Monto"
                                                        v-model="payment.amount"
                                                        v-validate
                                                        data-vv-rules="required"
                                                        :class="{'input-error': errors.has('amount' + id)}"
                                                        v-if="paymentEdit === payment.id"
                                                        >
                                                <p class="error" v-if="errors.firstByRule('amount' + id, 'required')">
                                                    Requerido
                                                </p>

                                                <span v-if="paymentEdit !== payment.id">
                                                    {{ '$' + payment.amount }}
                                                </span>
                                            </td>
                                            <td v-if="user.hasRole.admin">
                                                <!-- Editar pago -->
                                                <button
                                                        class="btn btn-warning"
                                                        @click="paymentEdit = payment.id"
                                                        :disabled="paymentEdit !== null"
                                                        v-if="paymentEdit !== payment.id"
                                                        >
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>

                                                <!-- Cancelar edicion de pago -->
                                                <button
                                                        class="btn btn-warning"
                                                        @click="paymentEdit = null"
                                                        v-if="paymentEdit === payment.id"
                                                        >
                                                    <i class="glyphicon glyphicon-remove-sign"></i>
                                                </button>
                                            </td>
                                            <td v-if="user.hasRole.admin">
                                                <!-- Guardar pago -->
                                                <button
                                                        class="btn btn-success"
                                                        v-if="paymentEdit === payment.id && paymentLoading !== payment.id"
                                                        @click="updatePayment(payment)"
                                                        >
                                                    <i class="glyphicon glyphicon-check"></i>
                                                </button>
                                                <img src="/img/loading.gif" v-if="paymentLoading === payment.id">

                                                <!-- Eliminar pago -->
                                                <button
                                                        type="button"
                                                        class="btn btn-danger"
                                                        data-toggle="modal"
                                                        data-target="#deleteModal"
                                                        v-if="paymentEdit !== payment.id"
                                                        @click="deleteId = payment.id"
                                                        >
                                                    <i class="glyphicon glyphicon-remove"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </section>

                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="patientModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <h3>
                                    <strong>
                                        Selecciona al paciente
                                    </strong>
                                </h3>
                            </div>
                            <div class="col-sm-6">
                                <h3 class="text-right">
                                    <a href="/user/patient/create">
                                        <i class="glyphicon glyphicon-plus"></i>
                                        Registrar paciente
                                    </a>
                                </h3>
                            </div>
                            <div class="col-xs-12">
                                <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Buscador"
                                        v-model="modal.search"
                                        @keyup="searchPatients()"
                                        >
                            </div>
                        </div>
                        <hr>

                        <div class="row">

                            <div class="col-xs-12">

                                <table class="table table-responsive table-striped">
                                    <thead>
                                    <tr>
                                        <th width="50%">Telefono</th>
                                        <th width="50%">Nombre</th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody v-if="! modal.loading">
                                    <tr v-for="p in modal.data" v-if="!patient || patient.id !== p.id">
                                        <td>{{ p.phone }}</td>
                                        <td>{{ p.name }}</td>
                                        <td>
                                            <button
                                                    class="btn btn-primary"
                                                    @click="selectPatient(p)"
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

        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="patientModal" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label for="service">Servicio</label>
                                    <select
                                            name="service"
                                            id="service"
                                            class="form-control"
                                            v-validate
                                            data-vv-rules="required"
                                            data-vv-scope="paymentModal"
                                            :class="{'input-error': errors.has('paymentModal.service')}"
                                            v-model="paymentModal.data.patient_history_id"
                                            @change="changePatientHistory()"
                                        >
                                        <option
                                                v-for="service in data.services"
                                                :value="service.id"
                                                v-if="service.pending_amount > 0"
                                                >
                                            {{ service.public_id + ' - ' + service.product.name + ' - $' + service.price }}
                                        </option>
                                    </select>

                                    <p class="error" v-if="errors.firstByRule('service', 'required', 'paymentModal')">
                                        Requerido
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-if="paymentModal.data.patient_history_id">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <p>
                                            <strong>Por pagar:</strong>
                                            ${{ paymentModalService.pending_amount }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label for="type">Tipo</label>
                                        <select
                                                name="type"
                                                id="type"
                                                class="form-control"
                                                v-validate
                                                data-vv-rules="required"
                                                data-vv-scope="paymentModal"
                                                :class="{'input-error': errors.has('paymentModal.type')}"
                                                v-model="paymentModal.data.type"
                                                >
                                            <option value="1">Tarjeta de crédito</option>
                                            <option value="2">Efectivo</option>
                                            <option value="3">Cheque</option>
                                            <option value="4">Descuento</option>
                                        </select>

                                        <p class="error" v-if="errors.firstByRule('type', 'required', 'paymentModal')">
                                            Requerido
                                        </p>
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="paymentDate">Fecha</label>
                                        <datepicker
                                                name = "paymentDate"
                                                id = "paymentDate"
                                                language="es"
                                                input-class = "form-control"
                                                format = "MM/dd/yyyy"
                                                @input="changePaymentDate($event)"
                                                v-model="initPaymentDate"
                                                :disabled="disabledDates"
                                                ></datepicker>
                                    </div>
                                </div>

                                <div class="col-xs-6">
                                    <div class="form-group">
                                        <label for="amount">Monto</label>
                                        <input
                                                v-for="service in data.services"
                                                v-if="service.id === paymentModal.data.patient_history_id"
                                                type="number"
                                                id="amount"
                                                name="amount"
                                                class="form-control"
                                                v-validate
                                                :data-vv-rules="'required|numeric|max_value:' + service.pending_amount"
                                                data-vv-scope="paymentModal"
                                                :class="{'input-error': errors.has('paymentModal.amount')}"
                                                v-model="paymentModal.data.amount"
                                                >
                                        <p class="error" v-if="errors.firstByRule('amount', 'required', 'paymentModal')">
                                            Requerido
                                        </p>
                                        <p class="error" v-if="errors.firstByRule('amount', 'numeric', 'paymentModal')">
                                            Formato invalido
                                        </p>
                                        <p class="error" v-if="errors.firstByRule('amount', 'max_value', 'paymentModal')">
                                            Maximo ${{ paymentModalService.pending_amount }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-success" @click="validatePayment()" v-show="! paymentModal.loading">Registrar</button>
                        <button id="closePaymentModal" type="button" class="btn btn-secondary" data-dismiss="modal" v-show="! paymentModal.loading">Cerrar</button>
                        <img src="/img/loading.gif" v-if="paymentModal.loading">
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4>¿Esta seguro de eliminar este pago?</h4>
                    </div>
                    <div class="modal-footer">
                        <button
                                type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal"
                                v-show="! loading"
                                @click="deleteId = null"
                                id="closeDeleteModal">
                            NO
                        </button>
                        <button
                                type="button"
                                class="btn btn-danger"
                                @click="sendDelete()"
                                v-show="! loading">
                            SI
                        </button>

                        <img src="/img/loading.gif" v-if="loading">
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
            user: {
                required: true,
                type: Object
            },
            products: {
                required: true,
                type: Array
            },
            doctors: {
                required: true,
                type: Array
            },
            assistants: {
                required: true,
                type: Array
            },
            secretaries: {
                required: true,
                type: Array
            }
        },
        data: function () {
          return {
              loading: false,
              patient: null,
              initStart: new Date(),
              initEnd: new Date(),
              initPaymentDate: new Date(),
              filter: false,
              data: {
                  start: '',
                  end: '',
                  services: [],
                  payments: []
              },
              modal: {
                  data: [],
                  loading: false,
                  search: ''
              },
              paymentModal: {
                  data: {
                      amount: null,
                      type: null,
                      patient_id: null,
                      patient_history_id: null,
                      date: null
                  },
                  loading: false,
              },
              deleteId: null,
              serviceEdit: null,
              serviceLoading: null,
              paymentEdit: null,
              paymentLoading: null,
              disabledDates: {}
          }
        },
        mounted: function () {
            const date = new Date();
            const day = date.getDate() >= 10 ? date.getDate() : '0' + date.getDate();
            const month = (date.getMonth() + 1) >= 10 ? (date.getMonth() + 1) : '0' + (date.getMonth() + 1);
            const year = date.getFullYear();

            this.data.start = year + '-' + month + '-' + day;
            this.data.end = year + '-' + month + '-' + day;
            this.paymentModal.data.date = year + '-' + month + '-' + day;

            const today = new Date();
            const yesterday = new Date(today.getTime() - 24*60*60*1000);
            this.disabledDates = this.user.hasRole.admin ? {} : {to: yesterday}
        },
        computed: {
            paymentModalService: function () {
                let selected = null;

                this.data.services.forEach((service) => {
                    if (service.id === this.paymentModal.data.patient_history_id) {
                        selected = service;
                    }
                });

                return selected;
            }
        },
        methods: {
            searchPatients: function () {
                this.modal.data = [];
                this.modal.loading = true;

                axios.get('/user/patient/budget/search?search=' + this.modal.search)
                    .then((res) => {
                        this.modal.loading = false;

                        this.modal.data = res.data.patients;
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.modal.loading = false;
                    })
                ;
            },

            selectPatient: function (patient) {
                this.patient = patient;
                this.paymentModal.data.patient_id = patient.id;
                this.data.services = [];
                this.data.payments = [];
                this.search();
            },

            changeStart: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.start = year + '-' + month + '-' + day;
            },

            changeEnd: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.end = year + '-' + month + '-' + day;
            },

            changePaymentDate: function (date) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.paymentModal.data.date = year + '-' + month + '-' + day;
            },

            changeDateService: function (date, index) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.services[index].created_at = year + '-' + month + '-' + day;
            },

            changeDatePayment: function (date, index) {
                let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
                let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
                let year = date.getFullYear();

                this.data.payments[index].date = year + '-' + month + '-' + day;
            },

            changePatientHistory: function () {
                this.paymentModal.data.amount = null;
                this.paymentModal.data.type = null;
            },

            search: function () {
                this.loading = true;
                this.serviceEdit = null;
                this.paymentEdit = null;

                axios.get('/user/payment/' + this.patient.public_id + '/search?start=' + this.data.start + '&end=' + this.data.end + '&all=' + !this.filter)
                    .then((res) => {
                        this.loading = false;

                        if (res.data.success) {
                            this.data.services = res.data.services;
                            this.data.payments = res.data.payments;

                            this.data.services.forEach((service) => {

                                let date = service.created_at;
                                let dp = new Date();

                                date = date.split(' ');
                                date = date[0].split('-');
                                dp.setFullYear(date[0], parseInt(date[1]) - 1, date[2]);

                                service.datePicker = dp;

                            });

                            this.data.payments.forEach((payment) => {

                                let date = payment.date;
                                let dp = new Date();

                                date = date.split(' ');
                                date = date[0].split('-');
                                dp.setFullYear(date[0], parseInt(date[1]) - 1, date[2]);

                                payment.datePicker = dp;

                            });
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.loading = false;
                        this.data.services = [];
                        this.data.payments = [];
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            validatePayment: function () {
                this.$validator.validateAll('paymentModal').then((res) => {

                    if (res) {
                        this.registerPayment();
                    }
                });
            },

            registerPayment: function () {

                this.paymentModal.loading = true;

                axios.post('/user/payment', this.paymentModal.data)
                    .then((res) => {
                        if (res.data.success) {
                            this.search();
                            $('#closePaymentModal').click();
                            this.paymentModal.data.amount = null;
                            this.paymentModal.data.type = null;
                            this.paymentModal.data.patient_history_id = null;
                            this.paymentModal.loading = false;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                        this.paymentModal.loading = false;
                    })
                ;
            },

            getTotalServices: function () {
                let total = 0;

                for (let i in this.data.services) {
                    total += parseInt(this.data.services[i].price);
                }

                return total;
            },

            getTotalPayments: function () {
                let total = 0;

                for (let i in this.data.payments) {
                    total += parseInt(this.data.payments[i].amount);
                }

                return total;
            },

            getBalance: function () {
                return this.getTotalPayments() - this.getTotalServices();
            },

            sendDelete: function () {
                this.loading = true;

                axios.delete('/user/payment/' + this.deleteId)
                    .then((res) => {

                        if (res.data.success) {
                            $('#closeDeleteModal').click();
                            this.filter = false;
                            this.search();
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.loading = false;
                        console.log(err);
                    })
            },

            updatePatientHistory: function (service) {
                this.serviceLoading = service.id;

                axios.put('/user/service/' + service.public_id + '/updateService', service)
                    .then((res) => {

                        this.updateRelation(service);

                        this.serviceLoading = null;
                        this.serviceEdit = null;

                        this.search();
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                    })
            },

            updatePayment: function (payment) {
                this.paymentLoading = payment.id;

                axios.put('/user/payment/' + payment.id, payment)
                    .then((res) => {

                        this.paymentLoading = null;
                        this.paymentEdit = null;
                        this.search();
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        console.log(err);
                    })
            },

            updateRelation: function (service) {
                // asignar producto
                for (let i in this.products) {
                    if (this.products[i].id === service.product_id) {
                        service.product = this.products[i];
                    }
                }

                // asignar doctor
                for (let i in this.doctors) {
                    if (this.doctors[i].id === service.doctor_id) {
                        service.doctor = this.doctors[i];
                    }
                }

                // asignar asistente
                for (let i in this.assistants) {
                    if (this.assistants[i].id === service.assistant_id) {
                        service.assistant = this.assistants[i];
                    }
                }
            }
        }
    }
</script>