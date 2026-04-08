<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\LocaleController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\ContactController;
use \App\Http\Controllers\TicketController;
use \App\Http\Controllers\CompanyController;
use \App\Http\Controllers\AllowDomainRegistrationController;
use \App\Http\Controllers\TicketMessageController;
use \App\Http\Controllers\TicketAttachmentController;
use \App\Http\Controllers\FulllTestController;
use \App\Http\Controllers\FulllDemoController;
use App\Http\Controllers\LaMetricController;

// Locale routes (accessible sans authentification)
Route::post('/locale/change', [LocaleController::class, 'change'])->name('locale.change');
Route::get('/locale/current', [LocaleController::class, 'current'])->name('locale.current');

// Public routes
Route::get('/', [PageController::class, 'home'])->name('home');
Route::post('/', [PageController::class, 'contact'])->name('contact.submit');
Route::get('/legal', [PageController::class, 'legal'])->name('legal');
Route::get('/rgpd', [PageController::class, 'rgpd'])->name('rgpd');
Route::get('/cgv', [PageController::class, 'cgv'])->name('cgv');
Route::get('/cgu', [PageController::class, 'cgu'])->name('cgu');
Route::get('/flyer', [PageController::class, 'flyer'])->name('flyer');
Route::get('/web', [PageController::class, 'web'])->name('web');
Route::get('/street', [PageController::class, 'street'])->name('street');
Route::get('/car', [PageController::class, 'car'])->name('car');
Route::get('/scannez-moi', [PageController::class, 'qrCode'])->name('qr-code');
Route::get('/cybersecurite', [PageController::class, 'cybersecurite'])->name('cybersecurite');
Route::post('/cybersecurite', [PageController::class, 'cybersecuriteSubmit'])->name('cybersecurite.submit');
Route::get('/cybersecurite-resultat', [PageController::class, 'cybersecuriteResult'])->name('cybersecurite.result');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// SAML routes (simplified - idpName from .env)
Route::get('/saml2/login', [\App\Http\Controllers\Auth\Saml2SynologyController::class, 'login'])
    ->middleware(['throttle:60,1'])
    ->defaults('idpName', env('SAML_IDP_NAME', 'synology'))
    ->name('saml_login');
Route::post('/saml2/acs', [\App\Http\Controllers\Auth\Saml2SynologyController::class, 'acs'])
    ->middleware(['throttle:60,1'])
    ->defaults('idpName', env('SAML_IDP_NAME', 'synology'))
    ->name('saml_acs');
Route::get('/saml2/sls', [\App\Http\Controllers\Auth\Saml2SynologyController::class, 'sls'])
    ->middleware(['throttle:60,1'])
    ->defaults('idpName', env('SAML_IDP_NAME', 'synology'))
    ->name('saml_logout');
Route::get('/saml2/metadata', [\App\Http\Controllers\Auth\Saml2SynologyController::class, 'metadata'])
    ->middleware(['throttle:60,1'])
    ->defaults('idpName', env('SAML_IDP_NAME', 'synology'))
    ->name('saml_metadata');
    ->name('saml_metadata');

// Registration routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/register/pending', [RegisterController::class, 'pending'])->name('register.pending');
Route::get('/email/verify/{id}/{hash}', [RegisterController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/resend', [RegisterController::class, 'resend'])
    ->middleware(['throttle:6,1'])
    ->name('verification.resend');

// Password reset routes
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ForgotPasswordController::class, 'reset'])->name('password.update');

// Protected routes (dashboard requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [PageController::class, 'dashboard'])->name('dashboard');
});

// Routes User - Permissions gérées dans UserController::__construct()
Route::prefix('user')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/', [UserController::class, 'store'])->name('user.store');
    Route::get('/{user}', [UserController::class, 'show'])->name('user.show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    
    // Impersonation routes (super-admin only - permission gérée dans le controller)
    Route::post('/{user}/impersonate', [UserController::class, 'impersonate'])->name('user.impersonate');
    Route::post('/stop-impersonation', [UserController::class, 'stopImpersonation'])->name('user.stop-impersonation');
});

// Routes Contact - Permissions gérées dans ContactController::__construct()
Route::prefix('contact')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [ContactController::class, 'index'])->name('contact.index');
    Route::get('/create', [ContactController::class, 'create'])->name('contact.create');
    Route::post('/', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/{contact}', [ContactController::class, 'show'])->name('contact.show');
    Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('contact.edit');
    Route::put('/{contact}', [ContactController::class, 'update'])->name('contact.update');
    Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('contact.destroy');
});

// Routes Ticket - Permissions gérées dans TicketController::__construct()
Route::prefix('ticket')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [TicketController::class, 'index'])->name('ticket.index');
    Route::get('/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::post('/', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('/{ticket}', [TicketController::class, 'show'])->name('ticket.show');
    Route::get('/{ticket}/edit', [TicketController::class, 'edit'])->name('ticket.edit');
    Route::put('/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
    Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');
    Route::post('/{ticket}/resend-confirmation', [TicketController::class, 'resendConfirmation'])->name('ticket.resend-confirmation');
    Route::get('/{ticket}/merge', [TicketController::class, 'mergeForm'])->name('ticket.merge.form');
    Route::post('/{ticket}/merge', [TicketController::class, 'merge'])->name('ticket.merge');
});

// Routes Company - Permissions gérées dans CompanyController::__construct()
Route::prefix('company')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/create', [CompanyController::class, 'create'])->name('company.create');
    Route::post('/', [CompanyController::class, 'store'])->name('company.store');
    Route::get('/{company}', [CompanyController::class, 'show'])->name('company.show');
    Route::get('/{company}/edit', [CompanyController::class, 'edit'])->name('company.edit');
    Route::put('/{company}', [CompanyController::class, 'update'])->name('company.update');
    Route::delete('/{company}', [CompanyController::class, 'destroy'])->name('company.destroy');
});

// Permission management routes - Permissions gérées dans PermissionController::__construct()
Route::prefix('permissions')->middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/create', [\App\Http\Controllers\PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/', [\App\Http\Controllers\PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/matrix', [\App\Http\Controllers\PermissionController::class, 'matrix'])->name('permissions.matrix');
    Route::post('/matrix', [\App\Http\Controllers\PermissionController::class, 'updateMatrix'])->name('permissions.matrix.update');
    Route::get('/{permission}', [\App\Http\Controllers\PermissionController::class, 'show'])->name('permissions.show');
    Route::get('/{permission}/edit', [\App\Http\Controllers\PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/{permission}', [\App\Http\Controllers\PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/{permission}', [\App\Http\Controllers\PermissionController::class, 'destroy'])->name('permissions.destroy');
});

// Routes AllowDomainRegistration - Permissions gérées dans AllowDomainRegistrationController::__construct()
Route::prefix('allowdomain')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [AllowDomainRegistrationController::class, 'index'])->name('allowdomain.index');
    Route::get('/create', [AllowDomainRegistrationController::class, 'create'])->name('allowdomain.create');
    Route::post('/', [AllowDomainRegistrationController::class, 'store'])->name('allowdomain.store');
    Route::get('/{allowdomain}', [AllowDomainRegistrationController::class, 'show'])->name('allowdomain.show');
    Route::get('/{allowdomain}/edit', [AllowDomainRegistrationController::class, 'edit'])->name('allowdomain.edit');
    Route::put('/{allowdomain}', [AllowDomainRegistrationController::class, 'update'])->name('allowdomain.update');
    Route::delete('/{allowdomain}', [AllowDomainRegistrationController::class, 'destroy'])->name('allowdomain.destroy');
});




// Routes TicketMessage - Permissions gérées dans TicketMessageController::__construct()
// Note: Pas de route index - les messages se gèrent depuis les tickets
Route::prefix('ticketmessage')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/create/{ticket}/{internal?}', [TicketMessageController::class, 'create'])->name('ticketmessage.create');
    Route::post('/', [TicketMessageController::class, 'store'])->name('ticketmessage.store');
    Route::get('/{ticketmessage}', [TicketMessageController::class, 'show'])->name('ticketmessage.show');
    Route::get('/{ticketmessage}/edit', [TicketMessageController::class, 'edit'])->name('ticketmessage.edit');
    Route::put('/{ticketmessage}', [TicketMessageController::class, 'update'])->name('ticketmessage.update');
    Route::delete('/{ticketmessage}', [TicketMessageController::class, 'destroy'])->name('ticketmessage.destroy');
});

// Routes TicketAttachment - Permissions gérées dans TicketAttachmentController::__construct()
Route::prefix('ticketattachment')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [TicketAttachmentController::class, 'index'])->name('ticketattachment.index');
    Route::get('/ticket/{ticketId}/create', [TicketAttachmentController::class, 'create'])->name('ticketattachment.create');
    Route::post('/', [TicketAttachmentController::class, 'store'])->name('ticketattachment.store');
    Route::get('/{ticketattachment}', [TicketAttachmentController::class, 'show'])->name('ticketattachment.show');
    Route::get('/{ticketattachment}/download', [TicketAttachmentController::class, 'download'])->name('ticketattachment.download');
    Route::get('/{ticketattachment}/edit', [TicketAttachmentController::class, 'edit'])->name('ticketattachment.edit');
    Route::put('/{ticketattachment}', [TicketAttachmentController::class, 'update'])->name('ticketattachment.update');
    Route::delete('/{ticketattachment}', [TicketAttachmentController::class, 'destroy'])->name('ticketattachment.destroy');
});

// Routes Fulll Test - Test API pour comptabilité full.io
Route::prefix('fulll/test')->middleware(['throttle:60,1'])->group(function () {
    // Page de test - Accessible en dev sans authentification
    if (app()->isLocal()) {
        Route::get('/page', [FulllTestController::class, 'testPage'])->name('fulll.test.page');
    }
    
    // Routes API - Avec authentification
    Route::middleware('auth')->group(function () {
        Route::get('/connection', [FulllTestController::class, 'testConnection'])->name('fulll.test.connection');
        Route::get('/clients', [FulllTestController::class, 'listClients'])->name('fulll.test.list');
        Route::post('/clients', [FulllTestController::class, 'createClient'])->name('fulll.test.create');
        Route::get('/clients/{id}', [FulllTestController::class, 'getClient'])->name('fulll.test.get');
        Route::put('/clients/{id}', [FulllTestController::class, 'updateClient'])->name('fulll.test.update');
        Route::delete('/clients/{id}', [FulllTestController::class, 'deleteClient'])->name('fulll.test.delete');
    });
});

// Routes Fulll Demo - Documentation et exemples (dev only)
Route::prefix('fulll/demo')->middleware(['throttle:60,1'])->group(function () {
    if (app()->isLocal()) {
        Route::get('/', [FulllDemoController::class, 'index'])->name('fulll.demo');
        Route::get('/test-token/{token}', [FulllDemoController::class, 'testWithToken'])->name('fulll.demo.test');
    }
});

// Routes LaMetric - Affichage d'informations sur appareil LaMetric
Route::prefix('lametric')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/socrate', [LaMetricController::class, 'displaySocrate'])->name('lametric.socrate');
});
