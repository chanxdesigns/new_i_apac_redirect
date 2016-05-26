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

Route::get('/{status}/{projectid}/{respid}/{country}/{vendor?}', 'RespDataController@main');
Route::get('/{respid}/completed', 'ViewController@completed')->name('completed');
Route::get('/{respid}/terminated', 'ViewController@terminated')->name('terminated');
Route::get('/{respid}/quotafull', 'ViewController@quotafull')->name('quotafull');


