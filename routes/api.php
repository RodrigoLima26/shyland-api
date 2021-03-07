<?php

use App\Http\Middleware\CheckApiToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

Route::post('/user/register',           'App\Http\Controllers\UsersController@register');
Route::post('/user/login',              'App\Http\Controllers\UsersController@login');
Route::post('/user/recover',            'App\Http\Controllers\UsersController@recover');
Route::post('/user/recover/{token}',    'App\Http\Controllers\UsersController@recoverConfirm');
Route::post('/user/login/token/{token}','App\Http\Controllers\UsersController@loginWithToken');

Route::middleware([CheckApiToken::class])->group(function () {

    Route::get('/user/get-by-token',        'App\Http\Controllers\UsersController@getUserByApiToken');


    Route::get('/user/missions',            'App\Http\Controllers\MissionController@getUserActiveMissions');
});
