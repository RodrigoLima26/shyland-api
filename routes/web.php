<?php

use Illuminate\Support\Facades\Route;

Route::get('/auth/{media}/login',              'App\Http\Controllers\UsersController@socialLogin');
Route::get('/auth/{media}/callback',           'App\Http\Controllers\UsersController@socialLoginCallback');
