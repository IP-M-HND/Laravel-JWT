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
Route::group(['middleware'=>[],'prefix'=>'v1'],function(){
   Route::post('auth/register','AuthController@register');
   Route::post('auth/login','AuthController@login');
   Route::post('auth/refreshToken','AuthController@refreshToken');
   Route::post('auth/logout','AuthController@logout');
});

Route::group(['middleware'=>['jwt.auth'],'prefix'=>'v1'],function(){
    Route::get('user/profile','AuthController@profile');
});
