<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\ContactController;

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::post('/', [PageController::class, 'contact'])->name('contact.submit');
Route::get('/legal', [PageController::class, 'legal'])->name('legal');
Route::get('/rgpd', [PageController::class, 'rgpd'])->name('rgpd');
Route::get('/cgv', [PageController::class, 'cgv'])->name('cgv');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/pending', [RegisterController::class, 'pending'])->name('register.pending');
Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Protected routes (dashboard requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
});

Route::resource('company', \App\Http\Controllers\CompanyController::class);

Route::prefix('user')->group(function () {
    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:user.index')
        ->name('user.index');
    Route::get('/create', [UserController::class, 'create'])
        ->middleware('permission:user.create')
        ->name('user.create');
    Route::post('/', [UserController::class, 'store'])
        ->middleware('permission:user.create')
        ->name('user.store');
    Route::get('/{user}', [UserController::class, 'show'])
        ->middleware('permission:user.show')
        ->name('user.show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user.edit')
        ->name('user.edit');
    Route::put('/{user}', [UserController::class, 'update'])
        ->middleware('permission:user.edit')
        ->name('user.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:user.delete')
        ->name('user.destroy');
});

Route::prefix('contact')->group(function () {
    Route::get('/', [ContactController::class, 'index'])
        ->middleware('permission:contact.index')
        ->name('contact.index');
    Route::get('/create', [ContactController::class, 'create'])
        //->middleware('permission:contact.create')
        ->name('contact.create');
    Route::post('/', [ContactController::class, 'store'])
        //->middleware('permission:contact.create')
        ->name('contact.store');
    Route::get('/{contact}', [ContactController::class, 'show'])
        ->middleware('permission:contact.show')
        ->name('contact.show');
    Route::get('/{contact}/edit', [ContactController::class, 'edit'])
        ->middleware('permission:contact.edit')
        ->name('contact.edit');
    Route::put('/{contact}', [ContactController::class, 'update'])
        ->middleware('permission:contact.edit')
        ->name('contact.update');
    Route::delete('/{contact}', [ContactController::class, 'destroy'])
        ->middleware('permission:contact.delete')
        ->name('contact.destroy');
});
