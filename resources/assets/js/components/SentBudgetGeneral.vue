<template>
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <h1>
                    <i class="glyphicon glyphicon-send"></i>
                    Presupuestos enviados
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="panel panel-default">
                    <div class="panel-body">

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="start">Desde</label>
                                    <datepicker
                                            name = "start"
                                            id = "start"
                                            language="es"
                                            input-class = "form-control datepicker"
                                            format = "MM/dd/yyyy"
                                            @input="form.start = changeDate($event)"
                                            v-model="initStart"
                                    ></datepicker>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="end">Hasta</label>
                                    <datepicker
                                            name = "end"
                                            id = "end"
                                            language="es"
                                            input-class = "form-control datepicker"
                                            format = "MM/dd/yyyy"
                                            @input="form.end = changeDate($event)"
                                            v-model="initEnd"
                                    ></datepicker>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <label for="status">Estatus</label>
                                <select
                                    class="form-control"
                                    name="status"
                                    id="status"
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

                        <div class="row">
                            <div class="col-xs-12">
                                <button class="btn btn-primary" @click="search()" v-if="! searchLoading">
                                    <i class="glyphicon glyphicon-search"></i>
                                    Buscar
                                </button>
                                <img src="/img/loading.gif" v-if="searchLoading">
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-12">
                                <div class="call-budgets" v-for="(callBudget, i) in callBudgetData" :key="i">

                                    <div 
                                        class="name-call"
                                        :class="{
                                            'bg-primary text-primary': callBudget.status === 1,
                                            'bg-success text-success': callBudget.status === 2,
                                            'bg-danger text-danger': callBudget.status === 3,
                                            'bg-warning text-warning': callBudget.status === 4
                                        }"
                                    >
                                        <div class="row">
                                            <div class="col-xs-6">
                                                {{ callBudget.name }}
                                                (
                                                    <span v-if="callBudget.status === 1">Hizo cita</span>
                                                    <span v-if="callBudget.status === 2">Interesado</span>
                                                    <span v-if="callBudget.status === 3">No interesado</span>
                                                    <span v-if="callBudget.status === 4">Transferido</span>
                                                )
                                            </div>
                                            <div class="col-xs-6 text-right">
                                                Estimado: ${{ callBudget.amount }}
                                            </div>
                                        </div>
                                    </div>
                                    <hr>

                                   

                                    <div class="row">
                                        <div class="col-xs-11">
                                            
                                            <!-- SHOW -->
                                            <section v-if="! callBudgetSelected || callBudgetSelected.id !== callBudget.id">
                                                <div class="row info-call">
                                                    <div class="col-sm-4">
                                                        <strong>Teléfono:</strong> {{ callBudget.phone }}
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <strong>Email:</strong> {{ callBudget.email }}
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <strong>Fuente:</strong> {{ callBudget.call_budget_source.name }}
                                                    </div>
                                                </div>

                                                <div class="row info-call">
                                                    <div class="col-sm-4">
                                                        <strong>Ultima llamada:</strong> {{ dateFormat(callBudget.last_call) }}
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <strong>Proxima acción:</strong> {{ callBudget.next_action }}
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <strong>Proxima llamada:</strong> {{ dateFormat(callBudget.next_call) }}
                                                    </div>
                                                </div>

                                                <div class="row info-call">
                                                    <div class="col-sm-4">
                                                        <strong>Tratamiento:</strong> {{ callBudget.service }}
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <strong>Tipo de contacto:</strong>
                                                        <span v-if="callBudget.contact_type === 1">Teléfono</span>
                                                        <span v-if="callBudget.contact_type === 2">Email</span>
                                                    </div>

                                                    <div class="col-sm-4">
                                                        <strong>Vendedor:</strong> {{ callBudget.sell_manager ? callBudget.sell_manager.name : '' }}
                                                    </div>
                                                </div>

                                                <div class="row info-call">
                                                    <div class="col-sm-12" v-if="callBudget.call_log">
                                                        <strong 
                                                        >
                                                        Notas: ({{ callBudget.notes }})
                                                        </strong>
                                                        <div >
                                                            <div v-for="(note, i) in callStatusHistory" :key="i" >
                                                                <div 
                                                                    v-if="note.call_budget_id  === callBudget.id"
                                                                    
                                                                >
                                                                    <div 
                                                                        v-for="(statusHistory, i) in 
                                                                        note.status_history" :key="i"
                                                                        class="alert alert-info"
                                                                    >
                                                                        <p>
                                                                            <strong>
                                                                                <i 
                                                                                    v-if="statusHistory.status === 1"
                                                                                >
                                                                                Pendiente</i>
                                                                                <i 
                                                                                    v-if="statusHistory.status === 2"
                                                                                >
                                                                                Con cita</i>
                                                                                <i 
                                                                                    v-if="statusHistory.status === 3"
                                                                                >
                                                                                No interesado</i>
                                                                                <i 
                                                                                    v-if="statusHistory.status === 4"
                                                                                >
                                                                                No constesto</i>
                                                                                <i 
                                                                                    v-if="statusHistory.status === 5"
                                                                                >
                                                                                Volver a llamar</i>
                                                                                <small>
                                                                                    ({{ statusHistory.created_at }})
                                                                                </small>
                                                                            </strong><br>
                                                                            {{ statusHistory.note }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12" v-if="! callBudget.call_log">
                                                        <strong 
                                                        >
                                                        Nota: {{ callBudget.notes }}
                                                        </strong>
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- EDIT -->
                                            <section v-else>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="status">Estatus</label>
                                                            <select
                                                                class="form-control"
                                                                name="status"
                                                                id="status"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                :class="{'input-error': errors.has('status')}"
                                                                v-model="callBudgetSelected.status"
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

                                                    <div class="col-sm-3" v-if="callBudgetSelected.status === 2 && callBudget.status !== 2">
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
                                                                v-model="callBudgetSelected.contact_repeat"
                                                            >
                                                            <span class="error" v-if="errors.firstByRule('contact_repeat', 'required')">
                                                                Requerido
                                                            </span>
                                                            <span class="error" v-if="errors.firstByRule('contact_repeat', 'regex')">
                                                                Formato invalido
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <div class="col-sm-3" v-if="callBudgetSelected.status === 2 && callBudget.status !== 2">
                                                        <div class="form-group">
                                                            <label for="contact_type">Forma de contacto</label>
                                                            <select
                                                                class="form-control"
                                                                name="contact_type"
                                                                id="contact_type"
                                                                v-validate
                                                                data-vv-rules="required"
                                                                :class="{'input-error': errors.has('contact_type')}"
                                                                v-model="callBudgetSelected.contact_type"
                                                            >
                                                                <option :value="1">Teléfono</option>
                                                                <option :value="2">Email</option>
                                                            </select>
                                                            <span class="error" v-if="errors.firstByRule('contact_type', 'required')">
                                                                Requerido
                                                            </span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </section>

                                        </div>

                                        <div class="col-xs-1 text-center">
                                            <div class="form-group" v-if="loading === i">
                                                <img src="/img/loading.gif">
                                            </div>

                                            <div class="form-group" v-if="loading !== i">
                                                <!-- Editar -->
                                                <button
                                                    :class="{
                                                        'btn btn-primary': callBudget.status === 1,
                                                        'btn btn-success': callBudget.status === 2,
                                                        'btn btn-danger': callBudget.status === 3,
                                                        'btn btn-warning': callBudget.status === 4
                                                    }"
                                                    @click="editCallBudget(callBudget)"
                                                    v-if="! callBudgetSelected || callBudgetSelected.id !== callBudget.id"
                                                    :disabled="callBudgetSelected"
                                                >
                                                    <i class="glyphicon glyphicon-edit"></i>
                                                </button>

                                                <!-- Cancelar -->
                                                <button
                                                    class="btn btn-default"
                                                    v-if="callBudgetSelected && callBudgetSelected.id === callBudget.id"
                                                    @click="callBudgetSelected = null"
                                                >
                                                    <i class="glyphicon glyphicon-remove"></i>
                                                </button>
                                            </div>

                                            <div class="form-group" v-if="loading !== i">
                                                <!-- Guardar -->
                                                <button
                                                    :class="{
                                                        'btn btn-primary': callBudget.status === 1,
                                                        'btn btn-success': callBudget.status === 2,
                                                        'btn btn-danger': callBudget.status === 3,
                                                        'btn btn-warning': callBudget.status === 4
                                                    }"
                                                    v-if="callBudgetSelected && callBudgetSelected.id === callBudget.id"
                                                    @click="validateCallBudget(i)"
                                                >
                                                    <i class="glyphicon glyphicon-saved"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
       },
       data () {
           return {
               loading: false,
               searchLoading: false,
               initStart: new Date(),
               initEnd: new Date(),
               callBudgetData: [],
               callStatusHistory: [],
               callBudgetSelected: null,
               showDetails: null,
               form: {
                   start: null,
                   end: null,
                   status: null,
               }
           }
       },
        mounted () {
            this.form.start = this.changeDate(new Date())
            this.form.end = this.changeDate(new Date())
        },

       methods: {
           dateFormat(date) {
               if (! date) {
                   return ''
               }

                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

           changeDate(date) {
               let day = (date.getDate() < 10) ? '0' + date.getDate() : date.getDate();
               let month = ((date.getMonth() + 1) < 10) ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
               let year = date.getFullYear();

               return year + '-' + month + '-' + day;
           },

           

            search () {
                this.searchLoading = true

                axios.get(
                    '/user/callBudget/searchGeneral' +
                    '?start=' + this.form.start +
                    '&end=' + this.form.end +
                    '&status=' + this.form.status 
                )
                    .then(res => {

                        if (res.data.success) {
                            this.callBudgetData = res.data.callBudgets
                            this.callStatusHistory = res.data.callStatusHistory
                        } else {
                            this.callBudgetData = []
                            this.callStatusHistory = []
                        }

                        this.searchLoading = false
                    })
                    .catch(err => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                        this.callBudgetData = []
                        this.searchLoading = false;
                    })
            },

            showDetail (id) {
                this.showDetails = true
            },

            hideDetail (id) {
                this.showDetails = false
            }

       }
    }
</script>

<style lang="scss" scoped>
    .call-budgets {
        margin-bottom: 30px;
        
    }
    .name-call {
        padding: 7px;
        border-radius: 3px;
        font-weight: bold;
        font-size: 13px;
    }
    info-call {
        font-size: 11px;
    }
    hr {
        margin-top: 7px;
        margin-bottom: 7px;
    }
    .with-service {
        display: inline-block;
        border: solid 1px;
        border-radius: 4px;
        font-weight: bold;
        width: 30px;
        height: 30px;
        text-align: center;
        padding-top: 4px;
        cursor: pointer;
    }
    .bg-secondary {
        background-color: #ccc;
    }
</style>

