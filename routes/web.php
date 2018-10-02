<?php

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

Route::get('/', 'IndexController@index');
Route::resource('/sites', 'SiteController');

Route::get('/senhaunica/login', 'Auth\LoginController@redirectToProvider');
Route::get('/senhaunica/callback', 'Auth\LoginController@handleProviderCallback');
Route::post('/logout', 'Auth\LoginController@logout');

Route::get('/admin/sites', 'AdminController@listaSites');
Route::get('/admin/todos_sites', 'AdminController@listaTodosSites');
