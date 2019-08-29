<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <h1>
                        <i class="glyphicon glyphicon-th-list" v-if="! loading"></i>
                        <img src="/img/loading.gif" v-if="loading">
                        Registrar usuario
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
                                                    :class="{'input-error': errors.has('username') || usernameError}"
                                                    @keyup="usernameError = false"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('username', 'required')">
                                                Este campo es requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('username', 'min')">
                                                Minimo 5 caracteres
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('username', 'regex')">
                                                Formato invalido
                                            </p>
                                            <p class="error" v-if="! errors.has('username') && usernameError">
                                                Usuario ya esta siendo usado
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
                                                    :class="{'input-error': errors.has('name')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('name', 'required')">
                                                Este campo es requerido
                                            </p>
                                        </div>
                                    </div>
                                </div>

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
                                                    :class="{'input-error': errors.has('password')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('password', 'required')">
                                                Este campo es requerido
                                            </p>
                                            <p class="error" v-if="errors.firstByRule('password', 'confirmed')">
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
                                                    :class="{'input-error': errors.has('password_confirmation')}"
                                                    >
                                            <p class="error" v-if="errors.firstByRule('password_confirmation', 'required')">
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
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="management_inventory">¿Maneja inventario?</label>
                                            <div>
                                                <input type="checkbox" v-model="form.management_supply" id="management_inventory">
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

                                    <div class="col-sm-4" v-if="doctorRole && hasRole(doctorRole.id)">
                                        <div class="form-group">
                                            <label for="password">¿Externo?</label>
                                            <div>
                                                <input type="checkbox" v-model="form.external">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <img src="/img/loading.gif" v-if="loading">
                                        <button class="btn btn-success" v-if="!loading">
                                            <i class="glyphicon glyphicon-saved"></i>
                                            Registrar usuario
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
            roles: {
                type: Array,
                required: true
            }
        },

        data: function () {
            return {
                loading: false,
                usernameError: false,
                form: {
                    username: '',
                    name: '',
                    password: '',
                    password_confirmation: '',
                    external: false,
                    management_inventory: false,
                    management_supply: false,
                    roles: []
                },
                doctorRole: null,
                currentRole: null,
                formSent: false
            }
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
                this.$validator.validateAll().then((res) => {
                    if (res && this.form.roles.length) {
                        this.verifyUsername();
                    }
                })
            },

            sendForm : function () {
                axios.post('/admin/user', this.form)
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

            verifyUsername: function () {
                this.loading = true;

                axios.get('/admin/user/' + this.form.username + '/verify')
                    .then((res) => {
                        if (res.data.valid) {
                            this.sendForm();
                        } else {
                            this.loading = false;
                            this.usernameError = true;
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
            }
        }
    }
</script>