<?php

// Usuarios sin autenticacion
Auth::routes();
Route::get('/', function() {
    return redirect()->route('login');
});

// Usuarios autenticados
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');

    // Productos
    Route::get('product/validate/{product}/{id}', 'User\ProductController@validatePublicId');
    Route::get('product/list', 'User\ProductController@productList');
    Route::resource('product', 'User\ProductController');

    // Pacientes
    Route::resource('patient', 'User\PatientController');
    Route::get('patient/phone/{phone}/{id?}', 'User\PatientController@verifyPhone');
    Route::get('patient/budget/search', 'User\PatientController@search');

    // Historial de paciente
    Route::get('service/search', 'User\PatientHistoryController@search')->name('service.search');
    Route::get('service/{service}/search', 'User\PatientHistoryController@searchService');
    Route::delete('service/note/{note}', 'User\PatientHistoryController@deleteNote');
    Route::delete('service/image/{note}', 'User\PatientHistoryController@deleteImage');
    Route::put('service/{service}/updateService', 'User\PatientHistoryController@updatePatientHistory');
    Route::post('service/register/patientHistory', 'User\PatientHistoryController@registerPatientHistory');
    Route::get('service/{service}/uploadImage', 'User\PatientHistoryController@uploadImage')->name('service.upload');
    Route::post('service/{service}/uploadImage', 'User\PatientHistoryController@storeImage');
    Route::resource('service', 'User\PatientHistoryController');

    // Cotizacion
    Route::resource('budget', 'User\BudgetController');
    Route::get('budget/{budget}/generatePdf', 'User\BudgetController@generatePdf')->name('budget.pdf.generate');
    Route::get('budget/{budget}/downloadPdf', 'User\BudgetController@downloadPdf')->name('budget.pdf.download');

    // Configuracion
    Route::post('config/uploadLogo', 'User\ConfigController@uploadLogo')->name('config.logo');
    Route::put('config/business', 'User\ConfigController@updateBusiness')->name('config.business');
    Route::get('config', 'User\ConfigController@config')->name('config');
    Route::put('config/changePassword', 'User\ConfigController@changePassword')->name('config.changePassword');

    // Llamadas
    Route::get('callLog/search', 'Secretary\CallLogController@search')->name('callLog.search');
    Route::get('callLog/search/call', 'Secretary\CallLogController@searchCall');
    Route::resource('callLog', 'Secretary\CallLogController');

    // Insumos
    Route::resource('supply', 'Assistant\SupplyController');

    // Solicitud de insumos
    Route::get('supplyRequest/search', 'Assistant\SupplyRequestController@search')->name('supplyRequest.search');
    Route::get('supplyRequest/search/report', 'Assistant\SupplyRequestController@searchSupplyRequest');
    Route::resource('supplyRequest', 'Assistant\SupplyRequestController');

    // Pagos
    Route::get('payment/{patient}/search', 'Secretary\PaymentController@search');
    Route::resource('payment', 'Secretary\PaymentController');

    // Gastos
    Route::get('expense/{expense}/search', 'Secretary\ExpenseController@search');
    Route::post('expense/expenseCommission', 'Secretary\ExpenseController@expenseCommission');
    Route::resource('expense', 'Secretary\ExpenseController');

    // Proveedores
    Route::get('supplier/list', 'Secretary\SupplierController@supplierList');
    Route::resource('supplier', 'Secretary\SupplierController');

    // Preguntas
    Route::put('question/{id}/hide', 'User\QuestionController@hide');
    Route::resource('question', 'User\QuestionController');

    // Email
    Route::resource('email', 'User\EmailController');

    // Citas
    Route::resource('appointment', 'User\AppointmentController');
    Route::put('appointment/{id}/cancel', 'User\AppointmentController@cancel')->name('appointment.cancel');

    // Usuarios
    Route::get('user/search', 'Admin\UserController@search');
    Route::get('user/assistant', 'Admin\UserController@assistantList');

    // Referencia de pacientes
    Route::resource('patientReference', 'User\PatientReferenceController');

    // Notificaciones
    Route::get('notification', 'NotificationController@sendLabNotifications');
    Route::put('notification', 'NotificationController@markAsRead');
});

// Administrador
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

    // Usuarios
    Route::get('user/search', 'Admin\UserController@search');
    Route::resource('user', 'Admin\UserController');
    Route::put('user/{user}/password', 'Admin\UserController@changePassword');
    Route::put('user/{user}/schedule', 'Admin\UserController@schedule');
    Route::get('user/{username}/verify', 'Admin\UserController@verifyUsername');

    // Comisiones
    Route::get('commission/config', 'Admin\CommissionController@config')->name('commission.config');
    Route::put('commission/{doctor}', 'Admin\CommissionController@update')->name('commission.update');

    // Reportes
    Route::get('report/servicesAndPayments', 'Admin\ReportController@servicesAndPayments')->name('report.servicesAndPayments');
    Route::get('report/servicesAndPaymentsData', 'Admin\ReportController@servicesAndPaymentsData');
    Route::get('report/servicesAndPaymentsPerPatient', 'Admin\ReportController@servicesAndPaymentsPerPatient')->name('report.servicesAndPaymentsPerPatient');
    Route::get('report/servicesAndPaymentsPerPatientData', 'Admin\ReportController@servicesAndPaymentsPerPatientData');
    Route::get('report/doctorCommissions', 'Admin\ReportController@doctorCommissions')->name('report.doctorCommissions');
    Route::get('report/doctorCommissionsData', 'Admin\ReportController@doctorCommissionsData');
    Route::get('report/expenses', 'Admin\ReportController@expenses')->name('report.expenses');
    Route::get('report/expensesData', 'Admin\ReportController@expensesData');
    Route::get('report/payments', 'Admin\ReportController@payments')->name('report.payments');
    Route::get('report/paymentsData', 'Admin\ReportController@paymentsData');
    Route::get('report/guarantees', 'Admin\ReportController@guarantees')->name('report.guarantees');
    Route::get('report/guaranteesData', 'Admin\ReportController@guaranteesData');
    Route::get('report/patientsAndPatientsWithServices', 'Admin\ReportController@patientsAndPatientsWithServices')->name('report.patientsAndPatientsWithServices');
    Route::get('report/patientsAndPatientsWithServicesData', 'Admin\ReportController@patientsAndPatientsWithServicesData');
    Route::get('report/budgets', 'Admin\ReportController@budgets')->name('report.budgets');
    Route::get('report/budgetsData', 'Admin\ReportController@budgetsData');
    Route::get('report/servicesPaymentsAndExpenses', 'Admin\ReportController@servicesPaymentsAndExpenses')->name('report.servicesPaymentsAndExpenses');
    Route::get('report/servicesPaymentsAndExpensesData', 'Admin\ReportController@servicesPaymentsAndExpensesData');
    Route::get('report/servicesDiagnostics', 'Admin\ReportController@servicesDiagnostics')->name('report.servicesDiagnostics');
    Route::get('report/servicesDiagnosticsData', 'Admin\ReportController@servicesDiagnosticsData');
    Route::get('report/servicesSendLab', 'Admin\ReportController@servicesSendLab')->name('report.servicesSendLab');
    Route::get('report/servicesSendLabData', 'Admin\ReportController@servicesSendLabData');
});
