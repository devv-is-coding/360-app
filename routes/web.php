<?php

use Illuminate\Support\Facades\Route;

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

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::view('dashboard', 'dashboard')->name('dashboard');
    });

    require __DIR__.'/settings.php';
});
