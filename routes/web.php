<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// DEFAULT DASHBOARD (Redirects based on role)
Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'system_admin') return redirect()->route('admin.dashboard');
    if ($user->role === 'doctor') return redirect()->route('doctor.dashboard');
    if ($user->role === 'data_entry') return redirect()->route('data_entry.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// AUTHENTICATED ROUTES
Route::middleware('auth')->group(function () {
    
    // System Admin Routes
    Route::middleware('role:system_admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin_dashboard');
        })->name('admin.dashboard');
    });

    // Doctor Routes
    Route::middleware('role:doctor')->group(function () {
        Route::get('/doctor/dashboard', function () {
            return view('doctor_dashboard');
        })->name('doctor.dashboard');
    });

    // Data Entry Routes
    Route::middleware('role:data_entry')->group(function () {
        Route::get('/data-entry/dashboard', function () {
            return view('data_entry_dashboard');
        })->name('data_entry.dashboard');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
