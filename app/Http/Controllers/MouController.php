<?php

namespace App\Http\Controllers;

use App\Models\Mou;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class MouController extends Controller
{
    /**
     * Display a listing of MoU for Mitra.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'mitra') {
            return redirect()->route('admin.dashboard');
        }

        $mous = Mou::where('user_id', $user->id)
            ->latest()
            ->get();

        return Inertia::render('mitra/Mou/Index', [
            'mous' => $mous
        ]);
    }

    /**
     * Store a newly created MoU in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,docx,doc|max:5120', // 5MB
        ]);

        $path = $request->file('document')->store('mou-documents', 'public');

        Mou::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'document_path' => $path,
            'status' => 'proses',
        ]);

        return redirect()->back()->with('success', 'Pengajuan MoU berhasil dikirim.');
    }

    /**
     * Display a listing of MoU for Admin.
     */
    public function adminIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return redirect()->route('mitra.dashboard');
        }

        $query = Mou::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('status')) {
             $query->where('status', $request->status);
        }

        $mous = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('admin/kelola-mou/Index', [
            'mous' => $mous,
            'filters' => $request->only(['search', 'date', 'status'])
        ]);
    }

    /**
     * Update the status of the MoU.
     */
    public function updateStatus(Request $request, Mou $mou)
    {
        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'validity_period_start' => 'required_if:status,disetujui|date',
            'validity_period_end' => 'required_if:status,disetujui|date|after:validity_period_start',
        ]);

        $mou->update([
            'status' => $request->status,
            'validity_period_start' => $request->validity_period_start,
            'validity_period_end' => $request->validity_period_end,
        ]);

        // Send email notification
        if ($mou->user && $mou->user->email) {
             \Illuminate\Support\Facades\Mail::to($mou->user->email)->send(new \App\Mail\MouStatusNotification($mou));
        }

        return redirect()->back()->with('success', 'Status MoU berhasil diperbarui dan notifikasi email telah dikirim.');
    }
}
