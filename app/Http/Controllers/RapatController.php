<?php

namespace App\Http\Controllers;

use App\Models\PksSubmission;
use App\Models\Rapat;
use App\Models\User;
use App\Notifications\RapatScheduled;
use App\Http\Requests\RapatRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RapatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow admin users to access this page
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Get all rapat created by this admin
        $rapat = Rapat::with(['creator'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);
        
        // Append document URL to each rapat
        $rapat->getCollection()->each(function ($item) {
            $item->append('pks_document_url');
        });
        
        // Get all mitra users for invitation in the modal
        $mitraUsers = User::where('role', 'mitra')
            ->select('id', 'name', 'company')
            ->get();
            
        // Get PKS submissions that need meetings
        $pksSubmissions = PksSubmission::where('status', 'proses')
            ->with('user')
            ->get();
        
        return Inertia::render('admin/kelola-rapat/Index', [
            'rapat' => $rapat,
            'mitraUsers' => $mitraUsers,
            'pksSubmissions' => $pksSubmissions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow admin users to access this page
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Get recent rapat created by this admin (last 5)
        $recentRapat = Rapat::with(['creator'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
            
        // Get PKS submissions that need meetings
        $pksSubmissions = PksSubmission::where('status', 'proses')
            ->with('user')
            ->get();
            
        // Get all mitra users
        $mitraUsers = User::where('role', 'mitra')
            ->select('id', 'name', 'company')
            ->get();
        
        return Inertia::render('admin/kelola-rapat/Create', [
            'recentRapat' => $recentRapat,
            'pksSubmissions' => $pksSubmissions,
            'mitraUsers' => $mitraUsers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RapatRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow admin users to access this page
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        // Get validated data
        $validatedData = $request->validated();
        
        // Handle file upload if present
        $pksDocumentPath = null;
        if ($request->hasFile('pks_document')) {
            $pksDocumentPath = $request->file('pks_document')->store('pks_documents', 'public');
        }
        
        // Format tanggal_waktu for database storage
        $tanggalWaktu = Carbon::parse($validatedData['tanggal_waktu']);
        
        // Create the rapat
        $rapat = Rapat::create([
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'] ?? null,
            'tanggal_waktu' => $tanggalWaktu,
            'lokasi' => $validatedData['lokasi'] ?? null,
            'user_id' => $user->id,
            'status' => $validatedData['status'] ?? 'akan_datang',
            'pks_document_path' => $pksDocumentPath,
            'pks_submission_id' => $validatedData['pks_submission_id'] ?? null,
        ]);
        
        // Update PKS status to "proses" if mitra are invited and PKS submission exists
        if (!empty($validatedData['invited_mitra']) && !empty($validatedData['pks_submission_id'])) {
            $pksSubmission = PksSubmission::find($validatedData['pks_submission_id']);
            if ($pksSubmission && $pksSubmission->status !== 'proses') {
                // Store the old status
                $oldStatus = $pksSubmission->status;
                
                // Update the status
                $pksSubmission->update(['status' => 'proses']);
                
                // Create status history
                \App\Models\StatusHistory::create([
                    'pks_submission_id' => $pksSubmission->id,
                    'status' => 'proses',
                    'notes' => 'Status diubah karena rapat telah dibuat dengan mitra yang diundang',
                ]);
                
                // Notify the mitra about the status update
                $mitra = $pksSubmission->user;
                $mitra->notify(new \App\Notifications\PksStatusUpdated($pksSubmission, $oldStatus, 'proses'));
            }
        }
        
        // Attach invited mitra to the rapat
        if (!empty($validatedData['invited_mitra'])) {
            $rapat->invitedMitra()->attach($validatedData['invited_mitra']);
            
            // Send notifications to invited mitra
            $invitedMitraUsers = User::whereIn('id', $validatedData['invited_mitra'])->get();
            foreach ($invitedMitraUsers as $mitra) {
                $mitra->notify(new RapatScheduled($rapat));
            }
        }
        
        return redirect()->route('rapat.index')->with('success', 'Rapat berhasil dibuat dan notifikasi telah dikirim ke mitra yang diundang.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow admin users or invited mitra to access this page
        if (!$user->isAdmin() && !$rapat->invitedMitra->contains($user->id)) {
            return redirect()->route('dashboard');
        }
        
        $rapat->load(['creator', 'invitedMitra']);
        $rapat->append('pks_document_url');
        
        return Inertia::render('admin/kelola-rapat/Show', [
            'rapat' => $rapat,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to edit
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        $rapat->load(['invitedMitra']);
        $rapat->append('pks_document_url');
        
        // Format the tanggal_waktu for datetime-local input
        if ($rapat->tanggal_waktu instanceof Carbon) {
            $rapat->tanggal_waktu = $rapat->tanggal_waktu->format('Y-m-d\TH:i');
        } else {
            // Handle string format and ensure proper parsing
            try {
                $date = Carbon::parse($rapat->tanggal_waktu);
                $rapat->tanggal_waktu = $date->format('Y-m-d\TH:i');
            } catch (\Exception $e) {
                // If parsing fails, use the original value
                $rapat->tanggal_waktu = '';
            }
        }
        
        // Get all mitra users for invitation
        $mitraUsers = User::where('role', 'mitra')
            ->select('id', 'name', 'company')
            ->get();
            
        // Get PKS submissions that need meetings
        $pksSubmissions = PksSubmission::where('status', 'proses')
            ->with('user')
            ->get();
        
        return Inertia::render('admin/kelola-rapat/Edit', [
            'rapat' => $rapat,
            'mitraUsers' => $mitraUsers,
            'pksSubmissions' => $pksSubmissions,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RapatRequest $request, Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to update
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        // Get validated data
        $validatedData = $request->validated();
        
        // Handle file removal
        if ($request->shouldRemoveExistingDocument) {
            if ($rapat->pks_document_path) {
                Storage::disk('public')->delete($rapat->pks_document_path);
            }
            $pksDocumentPath = null;
        } else {
            // Keep existing document by default
            $pksDocumentPath = $rapat->pks_document_path;
        }
        
        // Handle file upload if present
        if ($request->hasFile('pks_document')) {
            // Delete old document if exists
            if ($rapat->pks_document_path && !$request->shouldRemoveExistingDocument) {
                Storage::disk('public')->delete($rapat->pks_document_path);
            }
            // Store new document
            $pksDocumentPath = $request->file('pks_document')->store('pks_documents', 'public');
        }
        
        // Format tanggal_waktu for database storage
        $tanggalWaktu = Carbon::parse($validatedData['tanggal_waktu']);
        
        // Update the rapat
        $rapat->update([
            'judul' => $validatedData['judul'],
            'deskripsi' => $validatedData['deskripsi'] ?? null,
            'tanggal_waktu' => $tanggalWaktu,
            'lokasi' => $validatedData['lokasi'] ?? null,
            'status' => $validatedData['status'],
            'pks_document_path' => $pksDocumentPath,
            'pks_submission_id' => $validatedData['pks_submission_id'] ?? null,
        ]);
        
        // Check if we need to update PKS status to "proses"
        // This happens when:
        // 1. PKS submission is associated with this meeting
        // 2. Mitra are invited (either newly or already existing)
        // 3. PKS submission status is not already "proses"
        if (!empty($validatedData['pks_submission_id'])) {
            $pksSubmission = PksSubmission::find($validatedData['pks_submission_id']);
            if ($pksSubmission && $pksSubmission->status !== 'proses') {
                // Check if there are invited mitra
                $hasInvitedMitra = false;
                if (!empty($validatedData['invited_mitra'])) {
                    $hasInvitedMitra = true;
                } else {
                    // Check if there were already invited mitra
                    $hasInvitedMitra = $rapat->invitedMitra()->count() > 0;
                }
                
                if ($hasInvitedMitra) {
                    // Store the old status
                    $oldStatus = $pksSubmission->status;
                    
                    // Update the status
                    $pksSubmission->update(['status' => 'proses']);
                    
                    // Create status history
                    \App\Models\StatusHistory::create([
                        'pks_submission_id' => $pksSubmission->id,
                        'status' => 'proses',
                        'notes' => 'Status diubah karena rapat dengan mitra telah diatur',
                    ]);
                    
                    // Notify the mitra about the status update
                    $mitra = $pksSubmission->user;
                    $mitra->notify(new \App\Notifications\PksStatusUpdated($pksSubmission, $oldStatus, 'proses'));
                }
            }
        }
        
        // Handle mitra invitations if provided
        if (isset($validatedData['invited_mitra'])) {
            // Sync the invited mitra (this will add new ones and remove ones not in the array)
            $rapat->invitedMitra()->sync($validatedData['invited_mitra']);
            
            // Send notifications to newly invited mitra
            $invitedMitraUsers = User::whereIn('id', $validatedData['invited_mitra'])->get();
            foreach ($invitedMitraUsers as $mitra) {
                $mitra->notify(new RapatScheduled($rapat));
            }
        }
        
        return redirect()->route('rapat.index')->with('success', 'Rapat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to delete
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        // Delete associated document if exists
        if ($rapat->pks_document_path) {
            Storage::disk('public')->delete($rapat->pks_document_path);
        }
        
        $rapat->delete();
        
        return redirect()->route('rapat.index')->with('success', 'Rapat berhasil dihapus.');
    }
}