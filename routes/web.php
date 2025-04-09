<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AccountTypeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountStatementController;
use App\Http\Controllers\ChangePasswordController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Account Types Routes
    Route::resource('account_types', AccountTypeController::class);
    
    // Accounts Routes
    Route::resource('accounts', AccountController::class);
    
    // JeTemp Import Routes
    Route::get('je-temps/import', [App\Http\Controllers\JeTempImportController::class, 'index'])->name('je-temps.import-form');
    Route::post('je-temps/import', [App\Http\Controllers\JeTempImportController::class, 'import'])->name('je-temps.import');
    Route::post('je-temps/truncate', [App\Http\Controllers\JeTempImportController::class, 'truncate'])->name('je-temps.truncate');
    Route::post('je-temps/convert', [App\Http\Controllers\JeTempImportController::class, 'convertToJournalEntries'])->name('je-temps.convert');

    // Account Statement Routes
    Route::get('account-statement', [AccountStatementController::class, 'index'])->name('account-statement.index');
    Route::post('account-statement/generate', [AccountStatementController::class, 'generate'])->name('account-statement.generate');
    
    // Change Password Routes
    Route::get('change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change.form');
    Route::post('change-password', [ChangePasswordController::class, 'changePassword'])->name('password.change');

});
