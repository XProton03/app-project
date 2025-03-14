<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/api/prospect-leads', [DashboardController::class, 'getProspectLeads']); // New route for fetching prospect leads
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/api/schedules', [DashboardController::class, 'getSchedules']); // New route for fetching schedules
Route::get('/activities', [DashboardController::class, 'getActivities'])->name('activities');
