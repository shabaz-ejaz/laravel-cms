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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Post API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::resource('v1/posts', 'Api\PostsController', ['as' => 'api']);
});

/*
|--------------------------------------------------------------------------
| Company API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::resource('v1/companies', 'Api\CompaniesController', ['as' => 'api']);
});


Route::post('register', 'Api\PassportController@register');
Route::post('login', 'Api\PassportController@login');
Route::post('set-password', 'Api\PassportController@setPassword');

Route::group(['middleware' => ['auth:api']], function() {
    Route::get('/users/me', 'Api\UserController@me');
    Route::put('/users/me', 'Api\UserController@update');
    Route::put('/users/{id}', 'Api\UserController@update');
});


Route::group(['namespace' => 'Cms'], function () {
    Route::get('pages', 'PagesController@allJson');
    Route::get('page/{url}', 'PagesController@showJson');
    Route::get('p/{url}', 'PagesController@show');
});



/*
|--------------------------------------------------------------------------
| Company API Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('/companies', 'Api\CompaniesController', ['as' => 'api']);
    Route::get('/company/documents', 'Api\CompaniesController@documents');
    Route::post('/company/upload-document', [
        'uses' => 'Api\CompaniesController@uploadDocument'
    ]);
});