<template>
    <div class="odt-notification" v-if="showNotification">
        <div class="odt-notification__content">

            <h4 class="alert alert-info">Recordatorio de contacto</h4>

            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Forma de contacto</th>
                        <th>Estatus</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="callBudget in callBudgets">
                        <td>{{ callBudget.name }}</td>
                        <td>{{ callBudget.phone }}</td>
                        <td>{{ callBudget.email }}</td>
                        <td>
                            <span v-if="callBudget.contact_type === 1">Teléfono</span>
                            <span v-if="callBudget.contact_type === 2">Email</span>
                        </td>
                        <td>
                            <span
                                :class="{
                                    'bg-primary text-primary': callBudget.status === 1,
                                    'bg-success text-success': callBudget.status === 2,
                                    'bg-danger text-danger': callBudget.status === 3,
                                    'bg-warning text-warning': callBudget.status === 4
                                }"
                            >
                                <span v-if="callBudget.status === 1">Hizo cita</span>
                                <span v-if="callBudget.status === 2">Interesado</span>
                                <span v-if="callBudget.status === 3">No interesado</span>
                                <span v-if="callBudget.status === 4">Transferido</span>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center">
                <hr>
                <button class="btn btn-success" @click="showNotification = false">
                    <i class="glyphicon glyphicon-ok"></i>
                    Entendido
                </button>
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

        data: function () {
            return {
                showNotification: true
            }
        },

        methods: {

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            dateTimeFormat: function (dateTime) {
                let time = (dateTime.split(' ')[1]).split(':');
                let date = this.dateFormat(dateTime);

                return date + ' ' + time[0] + ':' + time[1];
            }
        }
    }
</script>