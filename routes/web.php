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
    Route::resource('expense', 'Secretary\ExpenseController');

    // Proveedores
    Route::resource('supplier', 'Secretary\SupplierController');

    // Preguntas
    Route::resource('question', 'User\QuestionController');
});

// Administrador
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

    // Usuarios
    Route::get('user/search', 'Admin\UserController@search');
    Route::resource('user', 'Admin\UserController');
    Route::put('user/{user}/password', 'Admin\UserController@changePassword');
    Route::get('user/{username}/verify', 'Admin\UserController@verifyUsername');

    // Comisiones
    Route::get('commission/config', 'Admin\CommissionController@config')->name('commission.config');
    Route::put('commission/{doctor}', 'Admin\CommissionController@update')->name('commission.update');

    // Reportes
    Route::get('report/servicesAndPayments', 'Admin\ReportController@servicesAndPayments')->name('report.servicesAndPayments');
    Route::get('report/servicesAndPaymentsData', 'Admin\ReportController@servicesAndPaymentsData')->name('report.servicesAndPaymentsData');
});
