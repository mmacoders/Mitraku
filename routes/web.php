<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PksSubmissionController;
use App\Http\Controllers\RapatController;
use App\Http\Controllers\MitraController;
use App\Http\Controllers\Mitra\PksSubmissionController as MitraPksSubmissionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

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

// Settings route
Route::get('/settings', [ProfileController::class, 'edit'])
    ->middleware(['auth', 'verified'])
    ->name('settings');

// Kelola PKS route (replaces pengajuan-pks)
Route::get('/admin/kelola-pks', [PksSubmissionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('kelola.pks');

// Kelola Mitra route
Route::get('/admin/kelola-mitra', [MitraController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('kelola.mitra');

// Kelola Dokumen Pasca Rapat route
Route::get('/admin/kelola-dokumen-pasca-rapat', [RapatController::class, 'indexPostMeetingDocuments'])
    ->middleware(['auth', 'verified'])
    ->name('kelola.dokumen.pasca.rapat');
    
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
    
    // Post-meeting document handling routes
    Route::post('/admin/kelola-rapat/{rapat}/upload-draft', [RapatController::class, 'uploadDraftDocument'])->name('rapat.uploadDraftDocument');
    Route::post('/admin/kelola-rapat/{rapat}/set-signing-schedule', [RapatController::class, 'setSigningSchedule'])->name('rapat.setSigningSchedule');
    Route::post('/admin/kelola-rapat/{rapat}/upload-signed', [RapatController::class, 'uploadSignedDocument'])->name('rapat.uploadSignedDocument');
    
    // Document deletion routes
    Route::delete('/admin/kelola-rapat/{rapat}/delete-draft', [RapatController::class, 'deleteDraftDocument'])->name('rapat.deleteDraftDocument');
    Route::delete('/admin/kelola-rapat/{rapat}/delete-signed', [RapatController::class, 'deleteSignedDocument'])->name('rapat.deleteSignedDocument');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Debug route to check rapat data
    Route::get('/debug/rapat-data', function () {
        $allRapat = \App\Models\Rapat::all();
        $futureRapatCount = \App\Models\Rapat::where('status', 'akan_datang')
            ->where('tanggal_waktu', '>=', now())
            ->count();
            
        return response()->json([
            'all_rapat' => $allRapat,
            'future_rapat_count' => $futureRapatCount,
            'now' => now()->toDateTimeString()
        ]);
    });
    
    // Routes to serve uploaded PKS documents
    Route::get('/pks-documents/{path}', function (string $path) {
        // Security check: ensure the path is within the allowed directories
        if (!str_starts_with($path, 'pks-documents/') && 
            !str_starts_with($path, 'pks-final-documents/') && 
            !str_starts_with($path, 'pks_documents/') &&
            !str_starts_with($path, 'pks-drafts/') &&
            !str_starts_with($path, 'pks-signatures/')) {
            abort(403);
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }
        
        // Check if user is authenticated
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }
        
        // Find submission associated with this document
        $submission = \App\Models\PksSubmission::where('kak_document_path', $path)
            ->orWhere('mou_document_path', $path)
            ->orWhere('final_document_path', $path)
            ->orWhere('draft_document_path', $path)
            ->orWhere('signed_document_path', $path)
            ->first();
            
        // Also check rapat documents
        if (!$submission) {
            $rapat = \App\Models\Rapat::where('pks_document_path', $path)
                ->orWhere('draft_document_path', $path)
                ->orWhere('signed_document_path', $path)
                ->first();
            if ($rapat) {
                // Only admin or meeting creator can access rapat documents
                if ($user->role === 'admin' || $user->id === $rapat->user_id) {
                    return response()->file(Storage::disk('public')->path($path));
                }
                abort(403);
            }
        }
        
        if ($submission) {
            // Admins can access all documents
            if ($user->role === 'admin') {
                return response()->file(Storage::disk('public')->path($path));
            }
            
            // Mitra can only access their own documents
            if ($user->id === $submission->user_id) {
                return response()->file(Storage::disk('public')->path($path));
            }
        }
        
        // If we can't verify ownership, deny access
        abort(403);
    })->where('path', '.*')->name('pks.documents.serve');
    
    // PKS Submission Routes
    Route::get('/pks', function () {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('kelola.pks');
        }
        return redirect()->route('mitra.dashboard');
    })->name('pks.index.redirect');

    Route::get('/pks/create', [PksSubmissionController::class, 'create'])->name('pks.create');
    Route::post('/pks', [PksSubmissionController::class, 'store'])->name('pks.store');
    Route::get('/pks/{pksSubmission}', [PksSubmissionController::class, 'show'])->name('pks.show');
    Route::get('/pks/{pksSubmission}/edit', [PksSubmissionController::class, 'edit'])->name('pks.edit');
    Route::put('/pks/{pksSubmission}', [PksSubmissionController::class, 'update'])->name('pks.update');
    Route::put('/pks/{pksSubmission}/status', [PksSubmissionController::class, 'updateStatus'])->name('pks.updateStatus');
    Route::delete('/pks/{pksSubmission}', [PksSubmissionController::class, 'destroy'])->name('pks.destroy');
    
    // Mitra PKS Submission Detail Route
    Route::get('/mitra/pks/{pksSubmission}', [MitraPksSubmissionController::class, '__invoke'])->name('mitra.pks.show');
    
    // Demo route for PKS submission modal
    Route::get('/pks/modal-demo', function () {
        return Inertia::render('admin/kelola-pks/ModalDemo');
    })->name('pks.modal-demo');
});

require __DIR__.'/auth.php';