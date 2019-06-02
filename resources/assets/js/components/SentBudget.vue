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
                            <div class="col-xs-12">
                                <div class="call-budgets" v-for="(callBudget, i) in callBudgets" :key="i">

                                    <div 
                                        class="name-call"
                                        :class="{
                                            'bg-primary text-primary': callBudget.status === 1,
                                            'bg-success text-success': callBudget.status === 2,
                                            'bg-danger text-danger': callBudget.status === 3
                                        }"
                                    >
                                        <div class="row">
                                            <div class="col-xs-6">
                                                {{ callBudget.name }}
                                                (
                                                    <span v-if="callBudget.status === 1">Hizo cita</span>
                                                    <span v-if="callBudget.status === 2">Interesado</span>
                                                    <span v-if="callBudget.status === 3">No interesado</span>
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
                                                    <strong>Proxima llamada:</strong> {{ callBudget.next_call }}
                                                </div>
                                            </div>

                                            <div class="row info-call">
                                                <div class="col-sm-4">
                                                    <strong>Tratamiento:</strong> {{ callBudget.service }}
                                                </div>

                                                <div class="col-sm-8">
                                                    <strong>Notas:</strong> {{ callBudget.notes }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-xs-1 text-center">
                                                <button
                                                    :class="{
                                                        'btn btn-primary': callBudget.status === 1,
                                                        'btn btn-success': callBudget.status === 2,
                                                        'btn btn-danger': callBudget.status === 3
                                                    }"
                                                >
                                                    <i class="glyphicon glyphicon-edit"></i>
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
</template>

<script>
    export default {
       props: {
           callBudgets: {
               type: Array,
               required: true
           }
       },
       methods: {
           dateFormat(date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },
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
</style>

