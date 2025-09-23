<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LocaleController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\ContactController;
use \App\Http\Controllers\TicketController;
use \App\Http\Controllers\CompanyController;

// Locale routes (accessible sans authentification)
Route::post('/locale/change', [LocaleController::class, 'change'])->name('locale.change');
Route::get('/locale/current', [LocaleController::class, 'current'])->name('locale.current');

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::post('/', [PageController::class, 'contact'])->name('contact.submit');
Route::get('/legal', [PageController::class, 'legal'])->name('legal');
Route::get('/rgpd', [PageController::class, 'rgpd'])->name('rgpd');
Route::get('/cgv', [PageController::class, 'cgv'])->name('cgv');
Route::get('/scannez-moi', [PageController::class, 'qrCode'])->name('qr-code');

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

Route::prefix('ticket')->group(function () {
    Route::get('/', [TicketController::class, 'index'])
        ->middleware('permission:ticket.index')
        ->name('ticket.index');
    Route::get('/create', [TicketController::class, 'create'])
        ->middleware('permission:ticket.create')
        ->name('ticket.create');
    Route::post('/', [TicketController::class, 'store'])
        ->middleware('permission:ticket.create')
        ->name('ticket.store');
    Route::get('/{ticket}', [TicketController::class, 'show'])
        ->middleware('permission:ticket.show')
        ->name('ticket.show');
    Route::get('/{ticket}/edit', [TicketController::class, 'edit'])
        ->middleware('permission:ticket.edit')
        ->name('ticket.edit');
    Route::put('/{ticket}', [TicketController::class, 'update'])
        ->middleware('permission:ticket.edit')
        ->name('ticket.update');
    Route::delete('/{ticket}', [TicketController::class, 'destroy'])
        ->middleware('permission:ticket.delete')
        ->name('ticket.destroy');
});

Route::prefix('company')->group(function () {
    Route::get('/', [CompanyController::class, 'index'])
        ->middleware('permission:company.index')
        ->name('company.index');
    Route::get('/create', [CompanyController::class, 'create'])
        ->middleware('permission:company.create')
        ->name('company.create');
    Route::post('/', [CompanyController::class, 'store'])
        ->middleware('permission:company.create')
        ->name('company.store');
    Route::get('/{company}', [CompanyController::class, 'show'])
        ->middleware('permission:company.show')
        ->name('company.show');
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])
        ->middleware('permission:company.edit')
        ->name('company.edit');
    Route::put('/{company}', [CompanyController::class, 'update'])
        ->middleware('permission:company.edit')
        ->name('company.update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])
        ->middleware('permission:company.delete')
        ->name('company.destroy');
});
