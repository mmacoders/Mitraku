<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PksSubmissionController;
use App\Http\Controllers\RapatController;
use App\Http\Controllers\MitraController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    // Check if user is admin and redirect to admin dashboard
    if (Auth::check() && Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    // For mitra users, show their dashboard
    return redirect()->route('mitra.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/mitra/dashboard', [PksSubmissionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('mitra.dashboard');

Route::get('/admin/dashboard', [PksSubmissionController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('admin.dashboard');

// Kelola PKS route (replaces pengajuan-pks)
Route::get('/admin/kelola-pks', [PksSubmissionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('kelola.pks');

// Kelola Mitra route
Route::get('/admin/kelola-mitra', [MitraController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('kelola.mitra');

// API routes for admin dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/api/dashboard/summary', [PksSubmissionController::class, 'getDashboardSummary'])
        ->name('api.dashboard.summary');
    Route::get('/api/dashboard/submissions', [PksSubmissionController::class, 'getDashboardSubmissions'])
        ->name('api.dashboard.submissions');
    Route::get('/api/dashboard/chart-data', [PksSubmissionController::class, 'getChartData'])
        ->name('api.dashboard.chart-data');
    // Notification routes
    Route::get('/api/notifications', [PksSubmissionController::class, 'getNotifications'])
        ->name('api.notifications');
    Route::post('/api/notifications/{id}/read', [PksSubmissionController::class, 'markNotificationAsRead'])
        ->name('api.notifications.read');
    
    // Mitra management routes
    Route::post('/admin/mitra', [MitraController::class, 'store'])->name('mitra.store');
    Route::put('/admin/mitra/{mitra}', [MitraController::class, 'update'])->name('mitra.update');
    Route::delete('/admin/mitra/{mitra}', [MitraController::class, 'destroy'])->name('mitra.destroy');
});

// Kelola Rapat routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/kelola-rapat', [RapatController::class, 'index'])->name('rapat.index');
    Route::get('/admin/kelola-rapat/create', [RapatController::class, 'create'])->name('rapat.create');
    Route::post('/admin/kelola-rapat', [RapatController::class, 'store'])->name('rapat.store');
    Route::get('/admin/kelola-rapat/{rapat}', [RapatController::class, 'show'])->name('rapat.show');
    Route::get('/admin/kelola-rapat/{rapat}/edit', [RapatController::class, 'edit'])->name('rapat.edit');
    Route::put('/admin/kelola-rapat/{rapat}', [RapatController::class, 'update'])->name('rapat.update');
    Route::delete('/admin/kelola-rapat/{rapat}', [RapatController::class, 'destroy'])->name('rapat.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // PKS Submission Routes
    Route::get('/pks/create', [PksSubmissionController::class, 'create'])->name('pks.create');
    Route::post('/pks', [PksSubmissionController::class, 'store'])->name('pks.store');
    Route::get('/pks/{pksSubmission}', [PksSubmissionController::class, 'show'])->name('pks.show');
    Route::get('/pks/{pksSubmission}/edit', [PksSubmissionController::class, 'edit'])->name('pks.edit');
    Route::put('/pks/{pksSubmission}', [PksSubmissionController::class, 'update'])->name('pks.update');
    Route::put('/pks/{pksSubmission}/status', [PksSubmissionController::class, 'updateStatus'])->name('pks.updateStatus');
    Route::delete('/pks/{pksSubmission}', [PksSubmissionController::class, 'destroy'])->name('pks.destroy');
    
    // Demo route for PKS submission modal
    Route::get('/pks/modal-demo', function () {
        return Inertia::render('admin/kelola-pks/ModalDemo');
    })->name('pks.modal-demo');
});

require __DIR__.'/auth.php';