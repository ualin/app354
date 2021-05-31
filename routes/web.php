<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function(){

    Route::get('/users','UserController@index');
    Route::get('/rooms','RoomController@index');
    Route::get('/departments','DepartmentController@index');
    Route::get('/employees','EmployeeController@index');
    Route::get('/salaries','SalaryController@index');
});
