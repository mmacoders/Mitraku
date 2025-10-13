<?php

namespace App\Http\Controllers;

use App\Models\PksSubmission;
use App\Models\Rapat;
use App\Models\User;
use App\Notifications\RapatScheduled;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        
        // Get all mitra users
        $mitra = User::where('role', 'mitra')->get();
        
        // Get all rapat created by this admin
        $rapat = Rapat::with(['creator', 'invitedMitra'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);
        
        // Append document URL to each rapat
        $rapat->getCollection()->each(function ($item) {
            $item->append('pks_document_url');
        });
        
        return Inertia::render('admin/kelola-rapat/Index', [
            'rapat' => $rapat,
            'mitra' => $mitra, // Pass mitra data to the view
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
        
        // Get all mitra users
        $mitra = User::where('role', 'mitra')->get();
        
        // Get recent rapat created by this admin (last 5)
        $recentRapat = Rapat::with(['creator', 'invitedMitra'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();
            
        // Get PKS submissions that need meetings
        $pksSubmissions = PksSubmission::where('status', 'proses')
            ->with('user')
            ->get();
        
        return Inertia::render('admin/kelola-rapat/Create', [
            'mitra' => $mitra,
            'recentRapat' => $recentRapat,
            'pksSubmissions' => $pksSubmissions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow admin users to access this page
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_waktu' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'mitra' => 'required|array',
            'mitra.*' => 'exists:users,id',
            'pks_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ]);
        
        // Handle file upload if present
        $pksDocumentPath = null;
        if ($request->hasFile('pks_document')) {
            $pksDocumentPath = $request->file('pks_document')->store('pks_documents', 'public');
        }
        
        // Create the rapat
        $rapat = Rapat::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_waktu' => $request->tanggal_waktu,
            'lokasi' => $request->lokasi,
            'user_id' => $user->id,
            'status' => 'akan_datang',
            'pks_document_path' => $pksDocumentPath, // Add the document path to the rapat
        ]);
        
        // Attach invited mitra
        $rapat->invitedMitra()->attach($request->mitra, [
            'status_kehadiran' => 'belum_dikonfirmasi'
        ]);
        
        // Load relationships for notification
        $rapat->load('creator');
        
        // Notify invited mitra about the scheduled meeting
        foreach ($request->mitra as $mitraId) {
            $mitra = User::find($mitraId);
            if ($mitra) {
                $mitra->notify(new RapatScheduled($rapat));
            }
        }
        
        return redirect()->route('rapat.index')->with('success', 'Rapat berhasil dibuat.');
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
        
        // Get all mitra users
        $mitra = User::where('role', 'mitra')->get();
        
        $rapat->load(['invitedMitra']);
        $rapat->append('pks_document_url');
        
        return Inertia::render('admin/kelola-rapat/Edit', [
            'rapat' => $rapat,
            'mitra' => $mitra,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rapat $rapat)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Only allow the creator (admin) to update
        if ($rapat->user_id !== $user->id) {
            return redirect()->route('dashboard');
        }
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tanggal_waktu' => 'required|date',
            'lokasi' => 'nullable|string|max:255',
            'mitra' => 'required|array',
            'mitra.*' => 'exists:users,id',
            'status' => 'required|in:akan_datang,selesai,dibatalkan',
            'pks_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048', // 2MB max
        ]);
        
        // Handle file upload if present
        $pksDocumentPath = $rapat->pks_document_path; // Keep existing document by default
        if ($request->hasFile('pks_document')) {
            // Delete old document if exists
            if ($rapat->pks_document_path) {
                Storage::disk('public')->delete($rapat->pks_document_path);
            }
            // Store new document
            $pksDocumentPath = $request->file('pks_document')->store('pks_documents', 'public');
        }
        
        // Update the rapat
        $rapat->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal_waktu' => $request->tanggal_waktu,
            'lokasi' => $request->lokasi,
            'status' => $request->status,
            'pks_document_path' => $pksDocumentPath, // Update document path
        ]);
        
        // Sync invited mitra
        $rapat->invitedMitra()->sync($request->mitra);
        
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