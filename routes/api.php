<?php

use App\Http\Controllers\Api\Auth\LoginController as LoginCon;
use App\Http\Controllers\Api\CashbackController as CashbackCon;
use App\Http\Controllers\Api\LocationController as LocationCon;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Login route
Route::post('login', [LoginCon::class, 'login'])->name('api.login');

// Logout route protected by auth middleware
Route::post('logout', [LoginCon::class, 'logout'])->name('api.logout')->middleware('auth:api');

// One user will be generated by User seeder so not needed
// Route::post('/register', [RegisterCon::class, 'register])->name('api.register');



// Unauthenticated GET routes
Route::prefix('get')->name('api.get')->group(function () {
    // GET requests for locations
    Route::prefix('location')->name('location')->group(function () {
        Route::get('{postcode}/nearest', [LocationCon::class, 'nearest'])->name('nearest');
    });
});

// Unauthenticated POST routes
Route::prefix('post')->name('api.post')->group(function () {
    // POST requests for cashbacks
    Route::prefix('cashback')->name('cashback')->group(function () {
        Route::post('calculate', [CashbackCon::class, 'calculate'])->name('calculate');
    });
});



// Authenticated routes
Route::prefix('auth')->name('api.auth')->middleware(['auth:api'])->group(function () {
    // Authenticated GET routes
    Route::prefix('get')->name('get')->group(function () {
        Route::prefix('cashback')->name('cashback')->group(function () {
            Route::get('history', [CashbackCon::class, 'history'])->name('history');
        });
    });

    // Authenticated POST routes
    Route::prefix('post')->name('post')->group(function () {
        Route::prefix('location')->name('location')->group(function () {
            Route::post('create', [LocationCon::class, 'create'])->name('create');
        });
    });
});
