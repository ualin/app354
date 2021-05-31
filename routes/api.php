<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/users','API\\UserController@index');
    Route::get('/users/{id}','API\\UserController@get');
    Route::put('/users/{id}','API\\UserController@update');
    Route::post('/users','API\\UserController@store');
    
    Route::get('/rooms','API\\RoomController@index');
    Route::get('/rooms/{room}','API\\RoomController@get');
    Route::post('/rooms','API\\RoomController@store');
    Route::put('/rooms/{id}','API\\RoomController@update');
    Route::delete('/rooms/{id}','API\\RoomController@destroy');

    Route::get('/departments','API\\DepartmentController@index');
    Route::get('/departments/{id}','API\\DepartmentController@get');
    Route::get('/departments/{id}/rooms','API\\DepartmentController@getRooms');
    Route::post('/departments','API\\DepartmentController@store');
    Route::put('/departments/{id}','API\\DepartmentController@update');

    Route::get('/employees','API\\EmployeeController@index');
    Route::get('/employees/{id}','API\\EmployeeController@get');
    Route::get('/employees/{id}/salary','API\\EmployeeController@getSalaries');
    Route::post('/employees','API\\EmployeeController@store');
    Route::put('/employees/{id}','API\\EmployeeController@update');
    Route::delete('/employees/{id}','API\\EmployeeController@destroy');

    Route::get('/salaries','API\\SalaryController@index');
    Route::get('/salaries/{id}','API\\SalaryController@get');
    Route::post('/salaries','API\\SalaryController@store');
    Route::put('/salaries/{id}','API\\SalaryController@update');
});
