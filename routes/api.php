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

Route::post('/admin/login','App\Http\Controllers\AdminController@login');

Route::get( '/contact',                 'App\Http\Controllers\ContactController@listContacts');
Route::post('/contact',                 'App\Http\Controllers\ContactController@addContact');
Route::post('/contact/{contact}/read',  'App\Http\Controllers\ContactController@readContact');
Route::post('/contact/{contact}/reply', 'App\Http\Controllers\ContactController@replyContact');

// Prints
Route::get('/admin/dashboard/pdf/users-last-seven-days',    'App\Http\Controllers\PDFController@newUsersLastSevenDays');
Route::get('/admin/dashboard/pdf/missions-complete-by-rank','App\Http\Controllers\PDFController@missionsCompletedByRank');
Route::get('/admin/dashboard/pdf/missions-complete-by-type','App\Http\Controllers\PDFController@missionsCompletedByType');
Route::get('/admin/dashboard/pdf/private-notifications',    'App\Http\Controllers\PDFController@privatedNotificationsLastWeek');

Route::middleware([CheckApiToken::class])->group(function () {

    Route::get('/user/get-by-token',        'App\Http\Controllers\UsersController@getUserByApiToken');
    Route::get('/user/get-by-id/{user}',    'App\Http\Controllers\UsersController@getUserById');
    Route::post('/user/change-password',    'App\Http\Controllers\UsersController@changePassword');
    Route::post('/user/update',             'App\Http\Controllers\UsersController@updateUser');
    Route::post('/user/photo-upload',       'App\Http\Controllers\UsersController@photoUpload');

    Route::get(   '/user/missions',                         'App\Http\Controllers\MissionController@getUserActiveMissions');
    Route::get(   '/user/missions/all',                     'App\Http\Controllers\MissionController@getAllUserMissions');
    Route::get(   '/user/{player}/missions/all',            'App\Http\Controllers\MissionController@getAllUserMissionsById');
    Route::post(  '/user/missions/{playermission}/complete','App\Http\Controllers\MissionController@completeMission');
    Route::delete('/user/missions/{playermission}/abandon', 'App\Http\Controllers\MissionController@cancelMission');

    Route::get('/users',    'App\Http\Controllers\UsersController@getAllUsers');

    Route::get('/notifications',                           'App\Http\Controllers\NotificationController@getAllNotifications');
    Route::get('/notifications/not-read',                  'App\Http\Controllers\NotificationController@notReadNotifications');
    Route::post('/notifications/{notification}/read',      'App\Http\Controllers\NotificationController@readNotification');
    Route::post('/notifications/user/{user}/reply',        'App\Http\Controllers\NotificationController@replyNotification');
    Route::delete('/notifications/{notification}/delete',  'App\Http\Controllers\NotificationController@deleteNotification');

    Route::get('/friendship',                                       'App\Http\Controllers\UsersController@getUserFriendship');
    Route::get('/friendship/all',                                   'App\Http\Controllers\UsersController@getAllUserFriendships');
    Route::post('/friendship/notification/{user}',                  'App\Http\Controllers\UsersController@friendshipNotification');
    Route::post('/friendship/notification/{notification}/confirm',  'App\Http\Controllers\UsersController@confirmFriendship');
    Route::delete('/friendship/notification/{notification}/delete', 'App\Http\Controllers\UsersController@deleteFriendRequest');
    Route::delete('/friendship/{friend}/unfriend',                  'App\Http\Controllers\UsersController@unfriend');

    // Admin Routes
    Route::get('/admin/dashboard',                              'App\Http\Controllers\AdminDashboardController@getDashboard');

    // Missions
    Route::get('/admin/missions',               'App\Http\Controllers\MissionController@getMissions');
    Route::get('/admin/missions/{mission}',     'App\Http\Controllers\MissionController@getMission');
    Route::post('/admin/missions',              'App\Http\Controllers\MissionController@setMission');
    Route::delete('/admin/missions/{mission}',  'App\Http\Controllers\MissionController@deleteMissions');

    // Users
    Route::post('/admin/users/{user}/ban',  'App\Http\Controllers\UsersController@banUser');
    Route::post('/admin/users/{user}/unban','App\Http\Controllers\UsersController@unbanUser');

    // Notification
    Route::get('/admin/notification',                           'App\Http\Controllers\NotificationController@getAdminNotifications');
    Route::post('/admin/notification/{player}/system-message',  'App\Http\Controllers\NotificationController@systemMessage');

    //Automated Tests
    Route::post(  '/user/automated/missions/complete','App\Http\Controllers\MissionController@completeAutomatedMission');
    Route::delete(  '/user/automated/missions/cancel',  'App\Http\Controllers\MissionController@cancelAutomatedMission');

});
