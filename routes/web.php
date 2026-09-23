<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboard;
use App\Http\Controllers\DataEntry\DashboardController as DataEntryDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\ReferenceDataController;
use App\Http\Controllers\DataEntry\RecordController;
use App\Http\Controllers\AuditFormController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\Doctor\SurgicalStatisticsController;

Route::get('/', function () {
    return view('welcome');
});

// MAIN DASHBOARD REDIRECT
Route::get('/dashboard', function () {
    $user = auth()->user();
    return match($user->role) {
        'system_admin' => redirect()->route('admin.dashboard'),
        'doctor'       => redirect()->route('doctor.dashboard'),
        'data_entry'   => redirect()->route('data_entry.dashboard'),
        default        => abort(403)
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// AUTHENTICATED ROUTES
Route::middleware('auth')->group(function () {

    // --- SYSTEM ADMIN ---
    Route::middleware('role:system_admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Uses Admin/DashboardController
            Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
            Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
            Route::post('/users', [UserController::class, 'store'])->name('users.store');
            Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
            
            // Reference Data Management (CRUD)
            Route::get('/reference-data', [ReferenceDataController::class, 'index'])->name('reference-data.index');
            Route::post('/reference-data/{type}', [ReferenceDataController::class, 'store'])->name('reference-data.store');
            Route::put('/reference-data/{type}/{id}', [ReferenceDataController::class, 'update'])->name('reference-data.update');
            Route::delete('/reference-data/{type}/{id}', [ReferenceDataController::class, 'destroy'])->name('reference-data.destroy');
    });

    // --- DOCTOR ---
    Route::middleware('role:doctor')
        ->prefix('doctor')
        ->name('doctor.')
        ->group(function () {
            // Uses Doctor/DashboardController
            Route::get('/dashboard', [DoctorDashboard::class, 'index'])->name('dashboard');
            
            // View the list of clinical records and specific details of a record
            Route::get('/records', [RecordController::class, 'index'])->name('records.index');
            Route::get('/records/{surgeryId}', [RecordController::class, 'show'])->name('records.show');
            Route::get('/records/{surgeryId}/edit', [RecordController::class, 'edit'])->name('records.edit');
            Route::put('/records/{surgeryId}', [RecordController::class, 'update'])->name('records.update');
            Route::delete('/records/{surgeryId}', [RecordController::class, 'destroy'])->name('records.destroy');

            // Patients Directory & Patient Profile
            Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
            Route::get('/patients/suggestions', [PatientController::class, 'suggestions'])->name('patients.suggestions');
            Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

            // Access and submit the Anesthesia Audit form
            Route::get('/audit-form/start', [AuditFormController::class, 'startCreate'])->name('audit-form.start');
            Route::get('/audit-form', [AuditFormController::class, 'create'])->name('audit-form.create');
                Route::get('/audit-form/phn-suggestions', [AuditFormController::class, 'phnSuggestions'])->name('audit-form.phn-suggestions');
                Route::get('/audit-form/patient-lookup', [AuditFormController::class, 'lookupPatient'])->name('audit-form.patient-lookup');
            Route::post('/audit-form', [AuditFormController::class, 'store'])->name('audit-form.store');
            Route::get('/search', [GlobalSearchController::class, 'index'])->name('search');

            // Surgical Statistics & Trend Analysis
            Route::get('/surgical-statistics', [SurgicalStatisticsController::class, 'index'])->name('surgical-statistics.index');
    });

    // --- DATA ENTRY ---
    Route::middleware('role:data_entry')
        ->prefix('data-entry')
        ->name('data_entry.')
        ->group(function () {
            // Uses DataEntry/DashboardController
            Route::get('/dashboard', [DataEntryDashboard::class, 'index'])->name('dashboard');
            
            // View the list of clinical records and specific details of a record
            Route::get('/records', [RecordController::class, 'index'])->name('records.index');
            Route::get('/records/{surgeryId}', [RecordController::class, 'show'])->name('records.show');
            Route::get('/records/{surgeryId}/edit', [RecordController::class, 'edit'])->name('records.edit');
            Route::put('/records/{surgeryId}', [RecordController::class, 'update'])->name('records.update');
            Route::delete('/records/{surgeryId}', [RecordController::class, 'destroy'])->name('records.destroy');

            // Access and submit the Anesthesia Audit form
            Route::get('/audit-form/start', [AuditFormController::class, 'startCreate'])->name('audit-form.start');
            Route::get('/audit-form', [AuditFormController::class, 'create'])->name('audit-form.create');
                Route::get('/audit-form/phn-suggestions', [AuditFormController::class, 'phnSuggestions'])->name('audit-form.phn-suggestions');
                Route::get('/audit-form/patient-lookup', [AuditFormController::class, 'lookupPatient'])->name('audit-form.patient-lookup');
            Route::post('/audit-form', [AuditFormController::class, 'store'])->name('audit-form.store');
            Route::get('/search', [GlobalSearchController::class, 'index'])->name('search');
    });

    // Global Universal Search
    Route::get('/global-search', [GlobalSearchController::class, 'index'])->name('global-search.index');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



require __DIR__.'/auth.php';