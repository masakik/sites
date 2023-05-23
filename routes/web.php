<?php

use App\Http\Controllers\AvisoController;
use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\ComentarioController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\SiteController;

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

Route::get('/', [IndexController::class, 'index'])->name('home');
Route::resource('/sites', SiteController::class);

Route::post('/sites/{site}/install', [SiteController::class, 'installSite']);
Route::post('/sites/{site}/disable', [SiteController::class, 'disableSite']);
Route::post('/sites/{site}/enable', [SiteController::class, 'enableSite']);
// Route::get('/sites/{site}/changeowner', [SiteController::class, 'changeOwner']);
// Route::get('/sites/{site}/novoadmin', [SiteController::class, 'novoAdmin']);

Route::post('/sites/{site}/gerenciador', [SiteController::class, 'gerenciador']);
Route::post('/sites/{site}/wp-plugin', [SiteController::class, 'WpPlugin']);

Route::get('check', [SiteController::class, 'check']);

# rotas para chamados
Route::get('/chamados', [ChamadoController::class, 'admin']);
Route::get('/chamados/{chamado}', [ChamadoController::class, 'show'])->name('chamados.show');
Route::get('/chamados/{site}/create', [ChamadoController::class, 'create']);
// Route::get('/chamados/{site}/', [ChamadoController::class, 'index']);
Route::post('/chamados/{site}/', [ChamadoController::class, 'store'])->name('chamados.store');

# rotas comentários
Route::post('/comentarios/{chamado}/', [ComentarioController::class, 'store'])->name('comentarios.store');

Route::get('/emails', [EmailController::class, 'emails']);

# Rotas Avisos
Route::resource('/avisos', AvisoController::class);

# Configurações
Route::get('/settings', function () {
    return view('settings');
});
