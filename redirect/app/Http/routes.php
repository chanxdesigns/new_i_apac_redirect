<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/{status}/{projectid}/{respid}/{country}', 'RespDataController@main');
Route::get('/{respid}/completed', 'ViewController@completed')->name('completed');
Route::get('/{respid}/terminated', 'ViewController@terminated')->name('terminated');
Route::get('/{respid}/quotafull', 'ViewController@quotafull')->name('quotafull');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});
