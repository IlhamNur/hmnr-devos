<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    
    Route::resource('problems', App\Http\Controllers\ProblemController::class);
    Route::post('problems/{problem}/solutions', [App\Http\Controllers\SolutionController::class, 'store'])->name('solutions.store');
    Route::patch('problems/{problem}/solutions/{solution}/verify', [App\Http\Controllers\SolutionController::class, 'verify'])->name('solutions.verify');
    Route::delete('problems/{problem}/solutions/{solution}', [App\Http\Controllers\SolutionController::class, 'destroy'])->name('solutions.destroy');
    
    Route::post('snippets/create', [App\Http\Controllers\SnippetController::class, 'create'])->name('snippets.create.post');
    Route::resource('snippets', App\Http\Controllers\SnippetController::class);
    Route::patch('snippets/{snippet}/favorite', [App\Http\Controllers\SnippetController::class, 'favorite'])->name('snippets.favorite');
    
    Route::post('knowledge/create', [App\Http\Controllers\KnowledgeController::class, 'create'])->name('knowledge.create.post');
    Route::resource('knowledge', App\Http\Controllers\KnowledgeController::class);
    
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'index'])->name('search');
    Route::get('/search/related', [App\Http\Controllers\SearchController::class, 'related'])->name('search.related');

    // Labs
    Route::prefix('api-lab')->name('api-lab.')->group(function () {
        Route::get('/', [App\Http\Controllers\ApiLabController::class, 'index'])->name('index');
        Route::post('/execute', [App\Http\Controllers\ApiLabController::class, 'execute'])->name('execute');
        Route::delete('/{api_request}', [App\Http\Controllers\ApiLabController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('mvel-lab')->name('mvel-lab.')->group(function () {
        Route::get('/', [App\Http\Controllers\MvelLabController::class, 'index'])->name('index');
        Route::post('/execute', [App\Http\Controllers\MvelLabController::class, 'execute'])->name('execute');
        Route::delete('/{mvel_execution}', [App\Http\Controllers\MvelLabController::class, 'destroy'])->name('destroy');
    });
    
    Route::get('/settings', fn () => view('pages.settings'))->name('settings.index');
});
