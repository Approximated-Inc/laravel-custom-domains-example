<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Routes for your primary domain can go here
// Any request with a host that doesn't match the primary domain will keep looking for a match below.
// Set your primary domain in your env file (or get it some other way).
Route::domain(env('APP_PRIMARY_DOMAIN'))->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');
    
    Route::middleware('auth')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

});

// Routes for custom domains can go here
// see the CustomDomains middleware for details on how it matches to custom domains.
Route::middleware('custom_domains')->group(function () {
    Route::get('/', [PublicProfileController::class, 'show'])->name('public_profile.show');
});


require __DIR__.'/auth.php';
