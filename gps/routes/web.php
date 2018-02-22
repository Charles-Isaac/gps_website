<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
    $.ajax({url: '/controller/item', type: 'post', 
    data: {_token: $('meta[name="csrf-token"]').attr('content'), name: 'abc', conditioning: false, amountPerPackaging: 3}, 
    onSuccess: function(data) {console.log(data);}});
 */

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function() {
    Route::middleware('isController')->group(function() {
        Route::prefix('controller')->group(function() {
            Route::get('items', 'AdminController@getItem')                   ->name('makeItem');
            Route::get('trucks', 'AdminController@getTruck')                 ->name('makeTruck');
            Route::get('clients', 'AdminController@getClient')               ->name('makeClient');
            Route::get('commands', 'AdminController@getCommand')             ->name('makeCommand');
            Route::get('sessions', 'AdminController@getSession')             ->name('makeSession');
            Route::get('suppliers', 'AdminController@getSupplier')           ->name('makeSupplier');
                                                                             
            Route::post('items', 'AdminController@MakeItem')                 ->name('postItem');    
            Route::post('trucks', 'AdminController@MakeTruck')               ->name('postTruck');   
            Route::post('clients', 'AdminController@MakeClient')             ->name('postClient');  
            Route::post('commands', 'AdminController@MakeCommand')           ->name('postCommand'); 
            Route::post('sessions', 'AdminController@MakeSession')           ->name('postSession');
            Route::post('suppliers', 'AdminController@MakeSupplier')         ->name('postSupplier');
            
            Route::post('editSessions', 'AdminController@EditSession')       ->name('postEditSession'); 
                                                                             
            Route::get('trucks/{id}', 'AdminController@getEditTruck')        ->name('makeTruck');
            Route::get('items/{id}', 'AdminController@getEditItem')          ->name('makeItem');
            Route::get('suppliers/{id}', 'AdminController@getEditSupplier')  ->name('makeSupplier');
            Route::get('clients/{id}', 'AdminController@getEditClient')      ->name('makeClient');
            Route::get('commands/{id}', 'AdminController@getEditCommand')    ->name('makeCommand');
            Route::get('sessions/{id}', 'AdminController@getEditSession')    ->name('makeSession');
            
            Route::get('home', 'AdminController@GetHome')->name('controllerHome');
        });
    });
    
    Route::prefix('truck')->group(function() {
        Route::middleware('mustHaveSession')->group(function() {
            Route::post('coords', 'TruckController@sendCoords');
            Route::post('reached', 'TruckController@reachedDest');
            Route::get('destinations', 'TruckController@getDestinations');
            Route::get('inventory', 'TruckController@getInventory');
            Route::get('home', 'TruckController@viewSession');
        });
        Route::get('pickSession', 'TruckController@chooseSession')->middleware('mustHaveNoSession')->name('chooseSession');
        Route::post('getSessionPath', 'TruckController@getSessionPath')->name('getSessionPath');
        Route::get('pickSession/{id}', 'TruckController@choseSession')->middleware('mustHaveNoSession')->name('chooseSession');
    });
});
Auth::routes();

Route::get('/', function () {
    return redirect('login');
});