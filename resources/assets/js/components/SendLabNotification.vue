<template>
    <div class="odt-notification" v-if="showNotification">
        <div class="odt-notification__content">

            <h4 class="alert alert-info">Recordatorio de entrega de laboratorio</h4>

            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>Entrega</th>
                        <th>Laboratorio</th>
                        <th>Doctor</th>
                        <th>Asistente</th>
                        <th>Responsable</th>
                        <th>Paciente</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="notification in notifications">
                        <td>{{ dateTimeFormat(notification.data.delivery_date.date) }}</td>
                        <td>{{ notification.data.supplier ? notification.data.supplier.name : '' }}</td>
                        <td>{{ notification.data.doctor ? notification.data.doctor.name : '' }}</td>
                        <td>{{ notification.data.assistant ? notification.data.assistant.name : '' }}</td>
                        <td>{{ notification.data.responsible ? notification.data.responsible.name : '' }}</td>
                        <td>{{ notification.data.patient ? notification.data.patient.name : '' }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center">
                <hr>
                <button class="btn btn-success" @click="markAsRead()">
                    <i class="glyphicon glyphicon-ok"></i>
                    Entendido
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                notifications: [],
                showNotification: false
            }
        },

        mounted: function () {

            this.getNotifications();

            window.setInterval(() => {

                if (! this.showNotification) {
                    this.getNotifications();
                }

            }, 60000);
        },

        methods: {

            getNotifications: function () {

                axios.get('/user/notification')
                    .then((res) => {

                        if (res.data.success) {
                            this.notifications = res.data.notifications;

                            if (this.notifications.length > 0) {
                                this.showNotification = true;
                            }
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                    })
            },

            dateFormat: function (date) {
                let format = date.split(' ');
                format = format[0].split('-');

                return format[1] + '/' + format[2] + '/' + format[0];
            },

            dateTimeFormat: function (dateTime) {
                let time = (dateTime.split(' ')[1]).split(':');
                let date = this.dateFormat(dateTime);

                return date + ' ' + time[0] + ':' + time[1];
            },

            markAsRead: function () {
                let data = [];

                for (let i in this.notifications) {
                    data.push(this.notifications[i].id);
                }

                axios.put('/user/notification', {notifications: data})
                    .then((res) => {

                        if (res.data.success) {
                            this.notifications = [];
                            this.showNotification = false;
                        }
                    })
                    .catch((err) => {
                        if (err.response.status === 403 || err.response.status === 405) {
                            location.href = '/';
                        }
                    })
            }
        }
    }
</script>