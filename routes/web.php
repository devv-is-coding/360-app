<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

/*
| Central routes are constrained to the central host so they do not collide with
| routes/tenant.php, which registers its routes without a domain constraint and
| is loaded later - a same-URI tenant route would otherwise overwrite the central
| one, taking its route name with it.
|
| A single host is used rather than looping over tenancy.central_domains because
| registering the same route name once per domain breaks `route:cache`.
*/
Route::domain((string) parse_url((string) config('app.url'), PHP_URL_HOST))->group(function () {
    Route::view('/', 'welcome')->name('home');

    /*
    | Super admin login. It posts through Fortify's login pipeline like /login does,
    | so throttling and two-factor apply; LoginPortal restricts it to super admins.
    */
    Route::middleware('guest:'.config('fortify.guard'))->group(function () {
        Route::view('admin/login', 'pages::auth.admin-login')->name('admin.login');

        Route::post('admin/login', [AuthenticatedSessionController::class, 'store'])
            ->middleware('throttle:'.config('fortify.limiters.login'))
            ->name('admin.login.store');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

    require __DIR__.'/settings.php';
});
