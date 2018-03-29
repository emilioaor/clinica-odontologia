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
    Route::resource('product', 'User\ProductController');

    // Pacientes
    Route::resource('patient', 'User\PatientController');
    Route::get('patient/phone/{phone}/{id?}', 'User\PatientController@verifyPhone');
    Route::get('patient/budget/search', 'User\PatientController@search');

    // Historial de paciente
    Route::get('service/search', 'User\PatientHistoryController@search')->name('service.search');
    Route::get('service/{service}/search', 'User\PatientHistoryController@searchService');
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

    // Solo administradores o secretarias
    Route::group(['middleware' => 'secretary'], function () {
        Route::resource('callLog', 'Secretary\CallLogController');
    });
});

// Administrador
Route::group(['prefix' => 'admin', 'middleware' => 'admin'], function () {

    // Usuarios
    Route::resource('user', 'Admin\UserController');
    Route::put('user/{user}/password', 'Admin\UserController@changePassword');
    Route::get('user/{username}/verify', 'Admin\UserController@verifyUsername');
});
