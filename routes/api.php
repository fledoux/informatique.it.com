<?php

use App\Http\Controllers\Api\CheckDomainController;
use Illuminate\Support\Facades\Route;

Route::post('/register/check-domain', [CheckDomainController::class, 'checkDomain'])->name('api.check-domain');
