<?php

namespace App\Http\Controllers;

use App\Models\PksSubmission;
use App\Models\Mou;
use App\Models\Rapat;
use App\Models\User;
use App\Notifications\RapatScheduled;
use App\Notifications\DraftDocumentUploaded;
use App\Notifications\SigningScheduleSet;
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
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow admin users to access this page
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Check if we're on the pasca rapat tab
        $isPascaTab = $request->tab === 'pasca';
        
        if ($isPascaTab) {
            // Handle pasca rapat tab
            return $this->handlePascaRapatTab($request, $user);
        }
        
        // Build query for rapat
        $query = Rapat::with(['creator', 'invitedMitra', 'pksSubmission', 'mou'])
            ->where('user_id', $user->id);
            
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        
        // Apply status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        // Apply date filter
        if ($request->has('date') && $request->date) {
            $query->whereDate('tanggal_waktu', $request->date);
        }
        
        // Get paginated results
        $rapat = $query->latest()->paginate(10);
        
        // Append document URLs
        $rapat->getCollection()->each(function ($item) {
            $item->append('pks_document_url');
            $item->append('draft_document_url');
            $item->append('signed_document_url');
        });
        
        // Get all mitra users for invitation
        $mitraUsers = User::where('role', 'mitra')
            ->select('id', 'name', 'company')
            ->get();
            
        // Get PKS submissions that need meetings
        $pksSubmissions = PksSubmission::where('status', 'proses')
            ->with('user')
            ->get();
            
        // Get MOU submissions that need meetings
        $mouSubmissions = Mou::where('status', 'proses')
            ->with('user')
            ->get();
            
        // Also get pasca rapat data for the second tab
        $pascaQuery = Rapat::with(['creator', 'pksSubmission', 'mou'])
            ->where('user_id', $user->id)
            ->where('status', 'selesai');
            
        // Apply search filter for pasca rapat
        if ($request->has('search_pasca') && $request->search_pasca) {
            $pascaQuery->where('judul', 'like', '%' . $request->search_pasca . '%');
        }
        
        // Apply process filter for pasca rapat
        if ($request->has('process') && $request->process) {
            switch ($request->process) {
                case 'draft_fix':
                    $pascaQuery->whereNotNull('draft_document_path');
                    break;
                case 'signing_schedule':
                    $pascaQuery->whereNotNull('signing_schedule');
                    break;
                case 'signed_document':
                    $pascaQuery->whereNotNull('signed_document_path');
                    break;
            }
        }
        
        $pascaRapat = $pascaQuery->latest()->paginate(10);
        
        // Append document URLs for pasca rapat
        $pascaRapat->getCollection()->each(function ($item) {
            $item->append('pks_document_url');
            $item->append('draft_document_url');
            $item->append('signed_document_url');
        });
        
        return Inertia::render('admin/kelola-rapat/Index', [
            'rapat' => $rapat,
            'pascaRapat' => $pascaRapat,
            'mitraUsers' => $mitraUsers,
            'pksSubmissions' => $pksSubmissions,
            'mouSubmissions' => $mouSubmissions,
        ]);
    }
    
    /**
     * Handle pasca rapat tab data.
     */
    private function handlePascaRapatTab(Request $request, User $user)
    {
        // Build query for rapat with status 'selesai'
        $query = Rapat::with(['creator', 'pksSubmission', 'mou'])
            ->where('user_id', $user->id)
            ->where('status', 'selesai');
            
        // Apply search filter
        if ($request->has('search_pasca') && $request->search_pasca) {
            $query->where('judul', 'like', '%' . $request->search_pasca . '%');
        }
        
        // Apply process filter
        if ($request->has('process') && $request->process) {
            switch ($request->process) {
                case 'draft_fix':
                    $query->whereNotNull('draft_document_path');
                    break;
                case 'signing_schedule':
                    $query->whereNotNull('signing_schedule');
                    break;
                case 'signed_document':
                    $query->whereNotNull('signed_document_path');
                    break;
            }
        }
        
        // Get paginated results
        $pascaRapat = $query->latest()->paginate(10);
        
        // Append document URLs
        $pascaRapat->getCollection()->each(function ($item) {
            $item->append('pks_document_url');
            $item->append('draft_document_url');
            $item->append('signed_document_url');
        });
        
        // Also get jadwal rapat data for the first tab
        $jadwalQuery = Rapat::with(['creator', 'invitedMitra', 'pksSubmission', 'mou'])
            ->where('user_id', $user->id);
            
        // Apply search filter for jadwal rapat
        if ($request->has('search') && $request->search) {
            $jadwalQuery->where('judul', 'like', '%' . $request->search . '%');
        }
        
        // Apply status filter for jadwal rapat
        if ($request->has('status') && $request->status) {
            $jadwalQuery->where('status', $request->status);
        }
        
        // Apply date filter for jadwal rapat
        if ($request->has('date') && $request->date) {
            $jadwalQuery->whereDate('tanggal_waktu', $request->date);
        }
        
        $rapat = $jadwalQuery->latest()->paginate(10);
        
        // Append document URLs for jadwal rapat
        $rapat->getCollection()->each(function ($item) {
            $item->append('pks_document_url');
            $item->append('draft_document_url');
            $item->append('signed_document_url');
        });
        
        // Get all mitra users for invitation
        $mitraUsers = User::where('role', 'mitra')
            ->select('id', 'name', 'company')
            ->get();
            
        // Get PKS submissions that need meetings
        $pksSubmissions = PksSubmission::where('status', 'proses')
            ->with('user')
            ->get();

        // Get MOU submissions that need meetings
        $mouSubmissions = Mou::where('status', 'proses')
            ->with('user')
            ->get();
        
        return Inertia::render('admin/kelola-rapat/Index', [
            'rapat' => $rapat,
            'pascaRapat' => $pascaRapat,
            'mitraUsers' => $mitraUsers,
            'pksSubmissions' => $pksSubmissions,
            'mouSubmissions' => $mouSubmissions,
        ]);
    }

    /**
     * Display a listing of post-meeting documents.
     */
    public function indexPostMeetingDocuments(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow admin users to access this page
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Build query for rapat with status 'selesai'
        $query = Rapat::with(['creator', 'pksSubmission', 'mou'])
            ->where('user_id', $user->id)
            ->where('status', 'selesai');
            
        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        
        // Apply process filter
        if ($request->has('process') && $request->process) {
            switch ($request->process) {
                case 'draft_fix':
                    $query->whereNotNull('draft_document_path');
                    break;
                case 'signing_schedule':
                    $query->whereNotNull('signing_schedule');
                    break;
                case 'signed_document':
                    $query->whereNotNull('signed_document_path');
                    break;
            }
        }
        
        // Get paginated results
        $rapat = $query->latest()->paginate(10);
        
        // Append document URLs
        $rapat->getCollection()->each(function ($item) {
            $item->append('pks_document_url');
            $item->append('draft_document_url');
            $item->append('signed_document_url');
        });
        
        return Inertia::render('admin/kelola-dokumen-pasca-rapat/Index', [
            'rapat' => $rapat,
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
            
        // Get MOU submissions that need meetings
        $mouSubmissions = Mou::where('status', 'proses')
            ->with('user')
            ->get();
            
        // Get all mitra users
        $mitraUsers = User::where('role', 'mitra')
            ->select('id', 'name', 'company')
            ->get();
        
        return Inertia::render('admin/kelola-rapat/Create', [
            'recentRapat' => $recentRapat,
            'pksSubmissions' => $pksSubmissions,
            'mouSubmissions' => $mouSubmissions,
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
            'mou_id' => $validatedData['mou_id'] ?? null,
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
        
        // Update MOU status to "proses" if mitra are invited and MOU submission exists
        if (!empty($validatedData['invited_mitra']) && !empty($validatedData['mou_id'])) {
            $mou = Mou::find($validatedData['mou_id']);
            if ($mou && $mou->status !== 'proses') {
                // Store the old status
                $oldStatus = $mou->status;
                
                // Update the status
                $mou->update(['status' => 'proses']);
                
                // Not using StatusHistory/Notifications for MOU yet as I'm not sure if they exist similarly
                // Assuming status 'proses' is correct.
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
        
        $rapat->load(['creator', 'invitedMitra', 'pksSubmission', 'mou']);
        $rapat->append('pks_document_url');
        $rapat->append('draft_document_url');
        $rapat->append('signed_document_url');
        
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
        
        $rapat->load(['invitedMitra', 'mou']);
        $rapat->append('pks_document_url');
        $rapat->append('draft_document_url');
        $rapat->append('signed_document_url');
        
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
        
        // Delete associated documents if exists
        if ($rapat->pks_document_path) {
            Storage::disk('public')->delete($rapat->pks_document_path);
        }
        
        if ($rapat->draft_document_path) {
            Storage::disk('public')->delete($rapat->draft_document_path);
        }
        
        if ($rapat->signed_document_path) {
            Storage::disk('public')->delete($rapat->signed_document_path);
        }
        
        $rapat->delete();
        
        return redirect()->route('rapat.index')->with('success', 'Rapat berhasil dihapus.');
    }
    
    /**
     * Upload draft document for a completed meeting.
     */
    public function uploadDraftDocument(Request $request, Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to upload draft document
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        // Validate the request
        $request->validate([
            'draft_document' => 'required|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ]);
        
        // Handle draft document upload
        if ($request->hasFile('draft_document')) {
            // Delete old draft document if exists
            if ($rapat->draft_document_path) {
                Storage::disk('public')->delete($rapat->draft_document_path);
            }
            
            // Store new draft document
            $draftDocumentPath = $request->file('draft_document')->store('pks_drafts', 'public');
            
            // Update the rapat
            $rapat->update([
                'draft_document_path' => $draftDocumentPath,
            ]);
            
            // Send notification to invited mitra
            $invitedMitraUsers = $rapat->invitedMitra;
            foreach ($invitedMitraUsers as $mitra) {
                $mitra->notify(new DraftDocumentUploaded($rapat));
            }
            
            return redirect()->back()->with('success', 'Draft dokumen berhasil diunggah dan notifikasi telah dikirim ke mitra yang diundang.');
        }
        
        return redirect()->back()->with('error', 'Gagal mengunggah draft dokumen.');
    }
    
    /**
     * Set signing schedule for a completed meeting.
     */
    public function setSigningSchedule(Request $request, Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to set signing schedule
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        // Validate the request
        $request->validate([
            'signing_schedule' => 'required|date',
        ]);
        
        // Format signing_schedule for database storage
        $signingSchedule = Carbon::parse($request->signing_schedule);
        
        // Update the rapat
        $rapat->update([
            'signing_schedule' => $signingSchedule,
        ]);
        
        // Send notification to invited mitra
        $invitedMitraUsers = $rapat->invitedMitra;
        foreach ($invitedMitraUsers as $mitra) {
            $mitra->notify(new SigningScheduleSet($rapat));
        }
        
        return redirect()->back()->with('success', 'Jadwal penandatanganan berhasil diatur dan notifikasi telah dikirim ke mitra yang diundang.');
    }
    
    /**
     * Upload signed document for a completed meeting.
     */
    public function uploadSignedDocument(Request $request, Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to upload signed document
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        // Validate the request
        $request->validate([
            'signed_document' => 'required|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ]);
        
        // Handle signed document upload
        if ($request->hasFile('signed_document')) {
            // Delete old signed document if exists
            if ($rapat->signed_document_path) {
                Storage::disk('public')->delete($rapat->signed_document_path);
            }
            
            // Store new signed document
            $signedDocumentPath = $request->file('signed_document')->store('pks_signatures', 'public');
            
            // Update the rapat
            $rapat->update([
                'signed_document_path' => $signedDocumentPath,
            ]);

            // Synchronize with PKS Submission
            if ($rapat->pks_submission_id) {
                $pksSubmission = PksSubmission::find($rapat->pks_submission_id);
                if ($pksSubmission) {
                    $oldStatus = $pksSubmission->status;
                    
                    // Update PKS Submission details
                    // We associate the same document path and set default validity if missing
                    $pksSubmission->update([
                        'status' => 'disetujui',
                        'final_document_path' => $signedDocumentPath,
                        // Set default validity to 1 year from now if not already set, to prevent data incompleteness
                        'validity_period_start' => $pksSubmission->validity_period_start ?? now(),
                        'validity_period_end' => $pksSubmission->validity_period_end ?? now()->addYear(),
                    ]);

                    // Add entry to Status History
                    \App\Models\StatusHistory::create([
                        'pks_submission_id' => $pksSubmission->id,
                        'status' => 'disetujui',
                        'notes' => 'Status otomatis disetujui setelah dokumen final diunggah melalui menu Rapat',
                    ]);

                    // Send notification to Mitra if status changed
                    if ($oldStatus !== 'disetujui') {
                         $mitra = $pksSubmission->user;
                         $mitra->notify(new \App\Notifications\PksStatusUpdated($pksSubmission, $oldStatus, 'disetujui'));
                    }
                }
            }

            // Synchronize with MOU
            if ($rapat->mou_id) {
                $mou = Mou::find($rapat->mou_id);
                if ($mou) {
                    $mou->update([
                        // 'status' => 'disetujui', // Disable auto-approve to allow manual decision with validity period
                        'document_path' => $signedDocumentPath,
                    ]);
                    
                    // No Status History for MOU yet
                }
            }
            
            return redirect()->back()->with('success', 'Dokumen yang ditandatangani berhasil diunggah. Status PKS otomatis diperbarui menjadi Disetujui.');
        }
        
        return redirect()->back()->with('error', 'Gagal mengunggah dokumen yang ditandatangani.');
    }
    
    /**
     * Delete draft document for a completed meeting.
     */
    public function deleteDraftDocument(Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to delete draft document
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        // Delete draft document if exists
        if ($rapat->draft_document_path) {
            Storage::disk('public')->delete($rapat->draft_document_path);
            
            // Update the rapat
            $rapat->update([
                'draft_document_path' => null,
            ]);
            
            return redirect()->back()->with('success', 'Draft dokumen berhasil dihapus.');
        }
        
        return redirect()->back()->with('error', 'Tidak ada draft dokumen untuk dihapus.');
    }
    
    /**
     * Delete signed document for a completed meeting.
     */
    public function deleteSignedDocument(Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to delete signed document
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        // Delete signed document if exists
        if ($rapat->signed_document_path) {
            Storage::disk('public')->delete($rapat->signed_document_path);
            
            // Update the rapat
            $rapat->update([
                'signed_document_path' => null,
            ]);
            
            return redirect()->back()->with('success', 'Dokumen yang ditandatangani berhasil dihapus.');
        }
        
        return redirect()->back()->with('error', 'Tidak ada dokumen yang ditandatangani untuk dihapus.');
    }
}