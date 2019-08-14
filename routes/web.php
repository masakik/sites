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

Route::get('/', 'IndexController@index')->name('home');
Route::resource('/sites', 'SiteController');
Route::resource('/chamados/{site}', 'ChamadoController');

# Senha única USP
Route::get('/senhaunica/login', 'Auth\LoginController@redirectToProvider')->name('login');
Route::get('/callback', 'Auth\LoginController@handleProviderCallback');
Route::post('/logout', 'Auth\LoginController@logout');
Route::get('/logout', 'Auth\LoginController@logout');

Route::post('/sites/{site}/clone', 'SiteController@cloneSite');
Route::post('/sites/{site}/disable', 'SiteController@disableSite');
Route::post('/sites/{site}/enable', 'SiteController@enableSite');
Route::post('/sites/{site}/delete', 'SiteController@deleteSite');
Route::get('/sites/{site}/changeowner', 'SiteController@changeOwner');
Route::get('/sites/{site}/novoadmin', 'SiteController@novoAdmin');

Route::get('check', 'SiteController@check');



