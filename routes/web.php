<?php

// Usuarios sin autenticacion
Auth::routes();
Route::get('/', function() {
    return redirect()->route('login');
});

// Usuarios autenticados
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::resource('product', 'User\ProductController');
    Route::resource('budget', 'User\BudgetController');
    Route::post('budget/uploadLogo', 'User\BudgetController@uploadLogo');
    Route::get('budget/{budget}/generatePdf', 'User\BudgetController@generatePdf');
});
