<?php

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

// test
Route::get('/test', 'Api\AuthController@test');

Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');
Route::get('/logout', 'Api\AuthController@logout');

Route::post('/member/account', 'Api\MemberController@memberByAccount');

Route::post('/friend/invite', 'Api\FriendController@invite');
Route::post('/friend/list', 'Api\FriendController@friends');
Route::post('/friend/listByInviter', 'Api\FriendController@friendsOfInviter');
Route::post('/friend/listByInvitee', 'Api\FriendController@friendsOfInvitee');
Route::post('/friend/accept', 'Api\FriendController@accept');

Route::post('/message/send', 'Api\MessageController@send');
Route::post('/message/list', 'Api\MessageController@message');
Route::post('/message/watch', 'Api\MessageController@watch');
