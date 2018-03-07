
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
import VeeValidate from 'vee-validate';

Vue.use(VeeValidate);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('register-product', require('./components/RegisterProduct.vue'));
Vue.component('edit-product', require('./components/EditProduct.vue'));
Vue.component('register-budget', require('./components/RegisterBudget.vue'));
Vue.component('edit-budget', require('./components/EditBudget.vue'));
Vue.component('change-password', require('./components/ChangePassword.vue'));
Vue.component('business-config', require('./components/BusinessConfig.vue'));
Vue.component('register-patient', require('./components/RegisterPatient.vue'));
Vue.component('edit-patient', require('./components/EditPatient.vue'));

const app = new Vue({
    el: '#app'
});
