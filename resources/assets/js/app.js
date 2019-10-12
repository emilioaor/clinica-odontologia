
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
Vue.component('edit-patient-history', require('./components/EditPatientHistory.vue'));
Vue.component('upload-image', require('./components/UploadImage.vue'));
Vue.component('create-patient-history', require('./components/CreatePatientHistory.vue'));
Vue.component('register-user', require('./components/RegisterUser.vue'));
Vue.component('edit-user', require('./components/EditUser.vue'));
Vue.component('search-patient-history', require('./components/SearchPatientHistory.vue'));
Vue.component('search-patient-history-admin', require('./components/SearchPatientHistoryAdmin.vue'));
Vue.component('register-call', require('./components/RegisterCall.vue'));
Vue.component('change-status-call', require('./components/CallChangeStatus.vue'));
Vue.component('search-call-log', require('./components/SearchCallLog.vue'));
Vue.component('register-supply', require('./components/RegisterSupply'));
Vue.component('edit-supply', require('./components/EditSupply'));
Vue.component('register-supply-request', require('./components/RegisterSupplyRequest'));
Vue.component('change-status-supply-request', require('./components/ChangeStatusSupplyRequest.vue'));
Vue.component('search-supply', require('./components/SearchSupply'));
Vue.component('register-payment', require('./components/RegisterPayment.vue'));
Vue.component('register-supplier', require('./components/RegisterSupplier.vue'));
Vue.component('edit-supplier', require('./components/EditSupplier.vue'));
Vue.component('register-expense', require('./components/RegisterExpense.vue'));
Vue.component('search-expense', require('./components/SearchExpense.vue'));
Vue.component('register-question', require('./components/RegisterQuestion.vue'));
Vue.component('edit-question', require('./components/EditQuestion.vue'));
Vue.component('config-commission', require('./components/ConfigCommission.vue'));
Vue.component('report-services-and-payments', require('./components/ReportServicesAndPayments.vue'));
Vue.component('report-services-and-payments-per-patient', require('./components/ReportServicesAndPaymentsPerPatient.vue'));
Vue.component('report-new-and-recurrent', require('./components/ReportNewAndRecurrent.vue'));
Vue.component('report-doctor-commissions', require('./components/ReportDoctorCommissions.vue'));
Vue.component('report-expenses', require('./components/ReportExpenses.vue'));
Vue.component('report-payments', require('./components/ReportPayments.vue'));
Vue.component('report-guarantees', require('./components/ReportGuarantees.vue'));
Vue.component('report-budgets', require('./components/ReportBudgets.vue'));
Vue.component('report-patients-with-services', require('./components/ReportPatientsAndPatientsWithService.vue'));
Vue.component('edit-email', require('./components/EditEmail.vue'));
Vue.component('register-appointment', require('./components/RegisterAppointment.vue'));
Vue.component('edit-appointment', require('./components/EditAppointment.vue'));
Vue.component('register-patient-reference', require('./components/RegisterPatientReference.vue'));
Vue.component('report-services-payments-expenses', require('./components/ReportServicesPaymentsAndExpenses.vue'));
Vue.component('report-services-diagnostics', require('./components/ReportServicesDiagnostics.vue'));
Vue.component('report-services-send-lab', require('./components/ReportServicesSendLab.vue'));
Vue.component('edit-supply-type', require('./components/EditSupplyType.vue'));
Vue.component('edit-supply-brand', require('./components/EditSupplyBrand.vue'));
Vue.component('supply-inventory-movement-in', require('./components/RegisterSupplyInventoryMovementIn.vue'));
Vue.component('supply-inventory-movement-out', require('./components/RegisterSupplyInventoryMovementOut.vue'));
Vue.component('draw-confirmation', require('./components/DrawConfirmation.vue'));
Vue.component('report-inventory-supply', require('./components/ReportInventorySupply.vue'));
Vue.component('report-inventory-supply-movement', require('./components/ReportInventorySupplyMovement.vue'));
Vue.component('report-sell-manager-patients', require('./components/ReportSellManagerPatients.vue'));
Vue.component('list-supply', require('./components/ListSupply.vue'));
Vue.component('register-call-budget-source', require('./components/RegisterCallBudgetSource.vue'));
Vue.component('sent-budget', require('./components/SentBudget.vue'));
Vue.component('sent-budget-table', require('./components/SentBudgetTable.vue'));
Vue.component('register-call-budget', require('./components/RegisterCallBudget.vue'));
Vue.component('edit-call-budget', require('./components/EditCallBudget.vue'));
Vue.component('report-call-log', require('./components/ReportCallLog.vue'));
Vue.component('register-tracking', require('./components/RegisterTracking.vue'));
Vue.component('list-tracking', require('./components/ListTracking.vue'));
Vue.component('send-lab-notification', require('./components/SendLabNotification.vue'));
Vue.component('call-budget-notification', require('./components/CallBudgetNotification.vue'));

const app = new Vue({
    el: '#app'
});
