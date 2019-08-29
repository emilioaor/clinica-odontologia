<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Actualizar usuario
                    </h1>
                </div>
            </div>

            <div class="row">

                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form @submit.prevent="validateForm()">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="username">Usuario</label>
                                            <input
                                                    type="text"
                                                    name="username"
                                                    id="username"
                                                    class="form-control"
                                                    placeholder="Nombre de usuario"
                                                    v-model="form.username"
                                                    v-validate
                                                    maxlength="20"
                                                    data-vv-rules="required|min:5|regex:^[a-z]{1}[a-z0-9]+$"
                                                    data-vv-scope="data"
                                                    :class="{'input-error': errors.has('data.username')}"
                                                    disabled
                                                    >
                                            <p class="error" v-if="errors.firstByRule('username', 'required', 'data')">
                                                Este campo es requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('username', 'min', 'data')">
                                                Minimo 5 caracteres
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('username', 'regex', 'data')">
                                                Formato invalido
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="username">Nombre</label>
                                            <input
                                                    type="text"
                                                    name="name"
                                                    id="name"
                                                    class="form-control"
                                                    placeholder="Nombre"
                                                    v-model="form.name"
                                                    v-validate
                                                    maxlength="60"
                                                    data-vv-rules="required"
                                                    data-vv-scope="data"
                                                    :class="{'input-error': errors.has('name')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('name', 'required', 'data')">
                                                Este campo es requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="username">Roles</label>
                                            <table class="table table-responsive">
                                                <tr v-for="(role, i) in form.roles" :key="role.id">
                                                    <td>
                                                        {{ role.name }}
                                                    </td>
                                                    <td class="text-center" width="5%">
                                                        <button type="button" class="btn btn-danger" @click="removeRole(i)">
                                                            <i class="glyphicon glyphicon-remove"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <select
                                                                name="level"
                                                                id="level"
                                                                class="form-control"
                                                                v-model="currentRole"
                                                                :class="{'input-error': formSent && ! form.roles.length}"
                                                        >
                                                            <option
                                                                    v-for="role in roles"
                                                                    :value="role.id"
                                                                    :key="role.id"
                                                                    v-if="! hasRole(role.id)"
                                                            >
                                                                {{ role.name }}
                                                            </option>
                                                        </select>
                                                        <p class="error" v-if="formSent && ! form.roles.length">
                                                            Este campo es requerido
                                                        </p>
                                                    </td>
                                                    <td class="text-center" width="5%">
                                                        <button type="button" class="btn btn-success" @click="addRole()">
                                                            <i class="glyphicon glyphicon-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-sm-4" v-if="hasRole(doctorRole.id)">
                                        <div class="form-group">
                                            <label for="external">¿Externo?</label>
                                            <div>
                                                <input type="checkbox" v-model="form.external" id="external">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="management_inventory">¿Maneja inventario?</label>
                                            <div>
                                                <input type="checkbox" v-model="form.management_inventory" id="management_inventory">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="management_inventory">¿Maneja insumo?</label>
                                            <div>
                                                <input type="checkbox" v-model="form.management_supply" id="management_supply">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <img src="/img/loading.gif" v-if="loading">
                                        <button class="btn btn-warning" v-if="!loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Actualizar usuario
                                        </button>

                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteUserModal" v-if="!loading">
                                            <i class="glyphicon glyphicon-remove"></i>
                                            Eliminar usuario
                                        </button>


                                        <!-- Modal -->
                                        <div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-body">
                                                        <h4>¿Esta seguro de eliminar a este usuario?</h4>
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
                                                                @click="deleteUser()"
                                                                v-if="! loading">
                                                            SI
                                                        </button>

                                                        <img src="/img/loading.gif" v-if="loading">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-10">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form @submit.prevent="validatePassForm()">

                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="password">Contraseña</label>
                                            <input
                                                    type="password"
                                                    name="password"
                                                    id="password"
                                                    class="form-control"
                                                    placeholder="Contraseña"
                                                    maxlength="20"
                                                    v-model="form.password"
                                                    v-validate
                                                    data-vv-rules="required|confirmed:password_confirmation"
                                                    data-vv-scope="pass"
                                                    :class="{'input-error': errors.has('pass.password')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('password', 'required', 'pass')">
                                                Este campo es requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('password', 'confirmed', 'pass')">
                                                Contraseñas no coinciden
                                            </p>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="password">Confirmar contraseña</label>
                                            <input
                                                    type="password"
                                                    name="password_confirmation"
                                                    id="password_confirmation"
                                                    class="form-control"
                                                    placeholder="Confirmar contraseña"
                                                    maxlength="20"
                                                    v-model="form.password_confirmation"
                                                    v-validate
                                                    data-vv-rules="required"
                                                    data-vv-scope="pass"
                                                    :class="{'input-error': errors.has('pass.password_confirmation')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('password_confirmation', 'required', 'pass')">
                                                Este campo es requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <img src="/img/loading.gif" v-if="loading">
                                        <button class="btn btn-success" v-if="!loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Cambiar contraseña
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-10 space-schedule">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <form @submit.prevent="validateScheduleForm()">

                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <label for="password">Habilitar restricci&oacute;n de horario</label>
                                            <input type="checkbox" v-model="form.login_schedule">
                                        </div>
                                    </div>
                                </div>

                                <div class="row" v-if="form.login_schedule">
                                    <div class="col-xs-12" v-for="(weekday, day) in configuredSchedule" :key="day">
                                        <div class="form-group">
                                            <p>
                                                <strong>
                                                    {{ translations[day] }} (desde - hasta)
                                                </strong>
                                            </p>

                                            <div class="row" v-for="(range, i) in weekday" v-if="weekday.length" :key="i">
                                                <div class="col-xs-5">
                                                    <div class="select-time">
                                                        <small>
                                                            Hora
                                                        </small>
                                                        <select
                                                                class="form-control"
                                                                v-model="range.timeStartHour"
                                                                @change="range.timeEndMinute = null;range.timeEndHour = null"
                                                                :name="'startHour' + day + i"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                data-vv-scope="schedule"
                                                                :class="{'input-error': errors.has('schedule.startHour' + day + i)}"
                                                                >
                                                            <option
                                                                    v-for="i in 24"
                                                                    :key="i"
                                                                    :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                                    >
                                                                {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                                            </option>
                                                        </select>

                                                        <p class="error" v-if="errors.firstByRule('startHour' + day + i, 'required', 'schedule')">
                                                            Requerido
                                                        </p>
                                                    </div>
                                                    <div class="select-time">
                                                        <small>
                                                            Minuto
                                                        </small>
                                                        <select
                                                                class="form-control"
                                                                v-model="range.timeStartMinute"
                                                                @change="changeScheduleStart(day, i)"
                                                                :name="'startMinute' + day + i"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                data-vv-scope="schedule"
                                                                :class="{'input-error': errors.has('schedule.startMinute' + day + i)}"
                                                                >
                                                            <option
                                                                    v-for="i in 60"
                                                                    :key="i"
                                                                    :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                                    >
                                                                {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                                            </option>
                                                        </select>

                                                        <p class="error" v-if="errors.firstByRule('startMinute' + day + i, 'required', 'schedule')">
                                                            Requerido
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-xs-5">
                                                    <div class="select-time">
                                                        <small>
                                                            Hora
                                                        </small>
                                                        <select
                                                                class="form-control"
                                                                v-model="range.timeEndHour"
                                                                :disabled="! range.timeStartHour || ! range.timeStartMinute"
                                                                :name="'endHour' + day + i"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                data-vv-scope="schedule"
                                                                :class="{'input-error': errors.has('schedule.endHour' + day + i)}"
                                                                >
                                                            <option
                                                                    v-for="i in 24"
                                                                    :key="i"
                                                                    :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                                    v-if="(i - 1) > range.timeStartHour"
                                                                    >
                                                                {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                                            </option>
                                                        </select>

                                                        <p class="error" v-if="errors.firstByRule('endHour' + day + i, 'required', 'schedule')">
                                                            Requerido
                                                        </p>
                                                    </div>
                                                    <div class="select-time">
                                                        <small>
                                                            Minuto
                                                        </small>
                                                        <select
                                                                class="form-control"
                                                                v-model="range.timeEndMinute"
                                                                :disabled="! range.timeStartHour || ! range.timeStartMinute"
                                                                :name="'endMinute' + day + i"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                data-vv-scope="schedule"
                                                                :class="{'input-error': errors.has('schedule.endMinute' + day + i)}"
                                                                >
                                                            <option
                                                                    v-for="i in 60"
                                                                    :key="i"
                                                                    :value="(i - 1) >= 10 ? (i - 1) : '0' + (i - 1)"
                                                                    >
                                                                {{ (i - 1) >= 10 ? (i - 1) : '0' + (i - 1) }}
                                                            </option>
                                                        </select>

                                                        <p class="error" v-if="errors.firstByRule('endMinute' + day + i, 'required', 'schedule')">
                                                            Requerido
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-xs-2">
                                                    <button type="button" class="btn btn-danger" @click="removeSchedule(day, i)">
                                                        <i class="glyphicon glyphicon-remove"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="row" v-if="! weekday.length">
                                                <div class="col-xs-12">
                                                    <button type="button" class="btn btn-default" @click="addSchedule(day)">
                                                        <i class="glyphicon glyphicon-plus"></i>
                                                        Agregar horario
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <img src="/img/loading.gif" v-if="loading">
                                        <button class="btn btn-success" v-if="!loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Guardar cambios
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>


<script>
    export default {
        props: {
            user: {
                type: Object,
                required: true
            },
            weekdays: {
                type: Array,
                required: true
            },
            translations: {
                type: Object,
                required: true
            },
            roles: {
                type: Array,
                required: true
            }
        },

        data: function () {
            return {
                loading: false,
                form: {},
                configuredSchedule: {
                    monday: [],
                    tuesday: [],
                    wednesday: [],
                    thursday: [],
                    friday: [],
                    saturday: [],
                    sunday: []
                },
                doctorRole: null,
                currentRole: null,
                formSent: false
            }
        },

        beforeMount: function () {
            this.form = this.user;
            this.initConfiguredSchedule();
        },

        mounted () {
            this.doctorRole = this.roles.find(role => role.code === 'doctor')
            this.currentRole = this.doctorRole.id
        },

        methods: {
            removeRole (index) {
                this.form.roles.splice(index, 1)
            },

            addRole () {
                if (! this.form.roles.find(role => role.id === this.currentRole)) {
                    this.form.roles.push(
                        this.roles.find(role => role.id === this.currentRole)
                    )
                }
            },

            hasRole (id) {
                return !! this.form.roles.find(role => role.id === id);
            },

            validateForm: function () {
                this.formSent = true;
                this.$validator.validateAll('data').then((res) => {
                    if (res && this.form.roles.length) {
                        this.sendForm();
                    }
                })
            },

            validatePassForm: function () {
                this.$validator.validateAll('pass').then((res) => {
                    if (res) {
                        this.sendPassForm();
                    }
                })
            },

            sendForm : function () {
                this.loading = true;

                axios.put('/admin/user/' + this.form.public_id, this.form)
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
                        console.log(err);
                    })
                ;
            },

            sendPassForm : function () {
                this.loading = true;

                axios.put('/admin/user/' + this.form.public_id + '/password', this.form)
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
                    console.log(err);
                })
                ;
            },

            deleteUser: function () {
                this.loading = true;

                axios.delete('/admin/user/' + this.form.public_id)
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
                        console.log(err);
                    })
            },

            initConfiguredSchedule: function () {
                let day;
                let start;
                let end;

                for (let i in this.user.weekdays) {

                    day = this.user.weekdays[i].weekday;
                    start = this.user.weekdays[i].pivot.time_start.split(':');
                    end = this.user.weekdays[i].pivot.time_end.split(':');

                    this.configuredSchedule[day].push({
                        timeStartHour: start[0],
                        timeStartMinute: start[1],
                        timeEndHour: end[0],
                        timeEndMinute: end[1]
                    });
                }
            },

            validateScheduleForm: function () {
                this.$validator.validateAll('schedule').then((res) => {
                    if (res) {
                        this.sendScheduleForm();
                    }
                })
            },

            sendScheduleForm : function () {
                this.loading = true;
                const data = {
                    configuredSchedule: this.configuredSchedule,
                    loginSchedule: this.form.login_schedule
                };

                axios.put('/admin/user/' + this.form.public_id + '/schedule', data)
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
                        console.log(err);
                    })
                ;
            },

            addSchedule: function (weekday) {
                this.configuredSchedule[weekday].push({
                    timeStartHour: null,
                    timeStartMinute: null,
                    timeEndHour: null,
                    timeEndMinute: null
                });
            },

            removeSchedule: function (weekday, index) {
                this.configuredSchedule[weekday].splice(index, 1);
            },

            changeScheduleStart: function (weekday, index) {
                this.configuredSchedule[weekday][index].timeEndHour = null;
                this.configuredSchedule[weekday][index].timeEndMinute = null;
            }
        }
    }
</script>

<style>
    .select-time {
        width: 50%;
        float: left;
    }
    .space-schedule .btn-danger {
        margin-top: 2rem;
    }
</style>