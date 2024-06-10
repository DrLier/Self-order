<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminMenuController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/self-order', [MenuController::class, 'showSelfOrder']);
Route::post('/orders/create', [OrderController::class, 'createOrder']);
Route::post('/orders/update/{menuId}', [OrderController::class, 'updateCart']);
Route::post('/orders/complete', [OrderController::class, 'completeOrder']);

Route::get('/admin/menus', [AdminMenuController::class, 'index']);
Route::get('/admin/menus/create', [AdminMenuController::class, 'create']);
Route::post('/admin/menus', [AdminMenuController::class, 'store']);
Route::get('/admin/menus/edit/{id}', [AdminMenuController::class, 'edit']);
Route::post('/admin/menus/update/{id}', [AdminMenuController::class, 'update']);
Route::delete('/admin/menus/{id}', [AdminMenuController::class, 'destroy']);
