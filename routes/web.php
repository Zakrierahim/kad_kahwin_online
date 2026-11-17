<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RsvpController;
use App\Http\Controllers\Admin\WeddingController;
use App\Http\Controllers\Admin\WishController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('admin.login.store');
});

Route::redirect('/admin', '/admin/dashboard');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/weddings', [WeddingController::class, 'index'])->name('weddings.index');
    Route::get('/weddings/create', [WeddingController::class, 'create'])->name('weddings.create');
    Route::post('/weddings', [WeddingController::class, 'store'])->name('weddings.store');
    Route::get('/weddings/{wedding}/edit', [WeddingController::class, 'edit'])->name('weddings.edit');
    Route::put('/weddings/{wedding}', [WeddingController::class, 'update'])->name('weddings.update');
    Route::delete('/weddings/{wedding}', [WeddingController::class, 'destroy'])->name('weddings.destroy');
    Route::patch('/weddings/{wedding}/status', [WeddingController::class, 'status'])->name('weddings.status');
    Route::get('/weddings/{wedding}/preview', [WeddingController::class, 'preview'])->name('weddings.preview');

    Route::get('/weddings/{wedding}/rsvps', [RsvpController::class, 'index'])->name('weddings.rsvps.index');
    Route::get('/weddings/{wedding}/rsvps/export', [RsvpController::class, 'export'])->name('weddings.rsvps.export');
    Route::delete('/weddings/{wedding}/rsvps/{rsvp}', [RsvpController::class, 'destroy'])
        ->scopeBindings()
        ->name('weddings.rsvps.destroy');

    Route::get('/weddings/{wedding}/wishes', [WishController::class, 'index'])->name('weddings.wishes.index');
    Route::delete('/weddings/{wedding}/wishes/{wish}', [WishController::class, 'destroy'])
        ->scopeBindings()
        ->name('weddings.wishes.destroy');
});

$slug = '[a-z0-9]+(?:-[a-z0-9]+)*';

Route::get('/{slug}', [InvitationController::class, 'show'])
    ->where('slug', $slug)
    ->name('invitation.show');

Route::post('/{slug}/rsvp', [InvitationController::class, 'rsvp'])
    ->where('slug', $slug)
    ->middleware('throttle:10,1')
    ->name('invitation.rsvp');

Route::post('/{slug}/wish', [InvitationController::class, 'wish'])
    ->where('slug', $slug)
    ->middleware('throttle:10,1')
    ->name('invitation.wish');
