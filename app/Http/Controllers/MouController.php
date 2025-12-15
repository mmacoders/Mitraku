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
            'validity_period_start' => 'required_if:status,disetujui|nullable|date',
            'validity_period_end' => 'required_if:status,disetujui|nullable|date|after:validity_period_start',
            'rejection_reason' => 'required_if:status,ditolak|nullable|string',
        ]);

        $mou->update([
            'status' => $request->status,
            'validity_period_start' => $request->validity_period_start ?: null,
            'validity_period_end' => $request->validity_period_end ?: null,
        ]);
       // Send email notification
        if ($mou->user && $mou->user->email) {
            try {
                $statusLabel = ucfirst($request->status);
                $color = $request->status == 'disetujui' ? '#16a34a' : '#dc2626';
                $appName = config('app.name');

                // Prepare additional info based on status
                $additionalInfo = '';
                if ($request->status === 'disetujui' && $request->validity_period_start && $request->validity_period_end) {
                    $start = \Carbon\Carbon::parse($request->validity_period_start)->translatedFormat('d F Y');
                    $end = \Carbon\Carbon::parse($request->validity_period_end)->translatedFormat('d F Y');
                    $additionalInfo = "<p><strong>Masa berlaku MoU:</strong><br>{$start} s/d {$end}</p>";
                } elseif ($request->status === 'ditolak' && $request->rejection_reason) {
                    $reason = nl2br(e($request->rejection_reason));
                    $additionalInfo = "<p><strong>Alasan Penolakan:</strong><br>{$reason}</p>";
                }

                $body = <<<HTML
                <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6; padding:30px 0; font-family: Arial, Helvetica, sans-serif;">
                  <tr>
                    <td align="center">
                      <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:6px; overflow:hidden;">
                        
                        <!-- HEADER -->
                        <tr>
                          <td style="background-color:#0f172a; padding:20px; color:#ffffff;">
                            <h2 style="margin:0; font-size:18px;">Update Status Pengajuan MoU</h2>
                          </td>
                        </tr>

                        <!-- BODY -->
                        <tr>
                          <td style="padding:24px; color:#111827; font-size:14px; line-height:1.6;">
                            <p style="margin-top:0;">Halo <strong>{$mou->user->name}</strong>,</p>

                            <p>
                              Pengajuan MoU Anda dengan judul:
                              <br>
                              <strong>"{$mou->title}"</strong>
                            </p>

                            <p>Status pengajuan Anda saat ini:</p>

                            <!-- STATUS BOX -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;">
                              <tr>
                                <td style="background-color:#f9fafb; border-left:5px solid {$color}; padding:12px;">
                                  <span style="font-size:16px; font-weight:bold; color:{$color};">
                                    {$statusLabel}
                                  </span>
                                </td>
                              </tr>
                            </table>

                            {$additionalInfo}

                            <p>
                              Silakan login ke aplikasi untuk melihat detail dan melanjutkan proses selanjutnya.
                            </p>

                            <p style="margin-bottom:0;">
                              Terima kasih,<br>
                              <strong>{$appName}</strong>
                            </p>
                          </td>
                        </tr>

                        <!-- FOOTER -->
                        <tr>
                          <td style="background-color:#f9fafb; padding:12px; font-size:12px; color:#6b7280; text-align:center;">
                            Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                          </td>
                        </tr>

                      </table>
                    </td>
                  </tr>
                </table>
                HTML;
            
                // Use GmailService to send the email
                $gmail = new \App\Services\GmailService();
                $gmail->sendEmail(
                    $mou->user->email,
                    'Update Status Pengajuan MoU',
                    $body
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Gagal mengirim email notifikasi MoU via Gmail API: ' . $e->getMessage());
                return redirect()->back()->with('success', 'Status MoU berhasil diperbarui, namun gagal mengirim notifikasi email.');
            }
        }

        return redirect()->back()->with('success', 'Status MoU berhasil diperbarui dan notifikasi email telah dikirim.');
    }
    public function destroy(Request $request, Mou $mou)
    {
        $user = $request->user();

        // Check ownership
        if ($user->role !== 'admin' && $user->id !== $mou->user_id) {
            abort(403);
        }

        // Only allow deleting 'proses' status for Mitra
        if ($user->role === 'mitra' && $mou->status !== 'proses') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status "Proses" yang dapat dihapus.');
        }

        // Delete file
        if ($mou->document_path) {
            Storage::disk('public')->delete($mou->document_path);
        }

        $mou->delete();

        return redirect()->back()->with('success', 'Pengajuan MoU berhasil dihapus.');
    }
}
