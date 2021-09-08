<?php

use App\Http\Controllers\AddSiteController;
use App\Http\Controllers\ProxyController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\SiteControllerAjax;
use Illuminate\Support\Facades\Route;

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
Route::middleware(['auth'])->group(function () {
Route::get('/',  [SiteController::class, 'index'])->name("dashboard");
Route::get('/proxy', [ProxyController::class, 'index']) ->name("add_proxy");
Route::get('/add_site', [AddSiteController::class, 'index'])->name("add_site");

//Добавить и удалить сайты
Route::post('/save_site', [AddSiteController::class, 'save'])->name("save_site");
Route::delete('/del_site/{id}', [AddSiteController::class, 'destroy'])->name("destroy_site");

//Добавить и удалить прокси
Route::post('/save_proxy', [ProxyController::class, 'save'])->name("save_proxy");
Route::delete('/del_proxy/{id}', [ProxyController::class, 'destroy'])->name("destroy_proxy");

//Роуты для работы с rabbitmq
Route::post('/in_wrapper', [SiteController::class, 'wrapper'])->name("in_wrapper");

//ajax
Route::post('/getSite/{id}', [SiteControllerAjax::class, 'index'])->name("getSiteAjax");
});

//Роуты для ответов от golang
Route::post('/api/url_status', [ApiController::class, 'status'])->name("api_url_status");

