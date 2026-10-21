<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\FairQueueDashboardController;
use App\Models\ScheduleJob;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    $jobs = ScheduleJob::query()
        ->where('group_key', 'user')
        ->where('group_value', (string) auth()->id())
        ->latest('created_at')
        ->limit(10)
        ->get();

    return view('home', compact('jobs'));
})->middleware(['auth', 'verified'])->name('home');

Route::post('/request-job', [ActionController::class, 'requestJob'])
    ->middleware(['auth', 'verified'])
    ->name('jobs.request');

Route::get('/admin/dashboard', FairQueueDashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');
