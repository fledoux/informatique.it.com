<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('company', \App\Http\Controllers\CompanyController::class);

Route::prefix('user')->group(function () {
    Route::get('/', [\App\Http\Controllers\UserController::class, 'index'])
        ->middleware('permission:user.index')
        ->name('user.index');
    Route::get('/create', [\App\Http\Controllers\UserController::class, 'create'])
        ->middleware('permission:user.create')
        ->name('user.create');
    Route::post('/', [\App\Http\Controllers\UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('user.store');
    Route::get('/{user}', [\App\Http\Controllers\UserController::class, 'show'])
        ->middleware('permission:user.show')
        ->name('user.show');
    Route::get('/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])
        ->middleware('permission:user.edit')
        ->name('user.edit');
    Route::put('/{user}', [\App\Http\Controllers\UserController::class, 'update'])
        ->middleware('permission:user.edit')
        ->name('user.update');
    Route::delete('/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('user.destroy');
});
