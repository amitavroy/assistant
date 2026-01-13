<?php

use App\Http\Controllers\FetchMailController;
use App\Http\Controllers\NewsletterController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');

    Route::resource('newsletters', NewsletterController::class)->only(['index', 'show']);
    Route::post('newsletters/fetch-mail', FetchMailController::class)->name('newsletters.fetch-mail');
});

require __DIR__.'/settings.php';
