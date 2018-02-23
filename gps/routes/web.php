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
            Route::post('item', 'AdminController@MakeItem');
            Route::post('truck', 'AdminController@MakeTruck');
            Route::post('client', 'AdminController@MakeClient');
            Route::post('createCommand', 'AdminController@MakeCommand');
            Route::post('createSession', 'AdminController@MakeSession');
            Route::post('createSupplier', 'AdminController@MakeSupplier');
            
            Route::post('stopSession', 'AdminController@StopSession');
            
            Route::get('pay', 'AdminController@Pay');
            Route::post('finishTransaction', 'AdminController@FinishTransaction');
            
            Route::post('addItemToCommand', 'AdminController@AddItemToSession');
            Route::post('addCommandToSession', 'AdminController@AddCommandToSession');
        });
    });
    
    Route::prefix('truck')->group(function() {
        Route::middleware('mustHaveSession')->group(function() {
            Route::post('coords', 'TruckController@sendCoords');
            Route::post('reached', 'TruckController@reachedDest');
            Route::get('destinations', 'TruckController@getDestinations');
            Route::get('inventory', 'TruckController@getInventory');
            Route::get('session', 'TruckController@viewSession');
        });
        Route::get('chooseTruck/{id}', 'TruckController@chooseTruck')->middleware('mustHaveNoSession')->name('chooseTruck');
        Route::get('home', 'TruckController@getHome')->middleware('mustHaveNoSession');
    });
});
Auth::routes();

Route::get('/', function () {
    return redirect('login');
});