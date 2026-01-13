<?php

namespace App\Http\Controllers;

use App\Models\PksSubmission;
use App\Models\StatusHistory;
use App\Models\User;
use App\Http\Requests\PksSubmissionRequest;
use App\Notifications\PksSubmitted;
use App\Notifications\PksStatusUpdated;
use App\Notifications\AdminPksSubmissionNotification; // Added this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Services\GmailService;
use Illuminate\Support\Facades\Log;
use App\Models\Rapat;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PksSubmissionController extends Controller
{
    /**
     * Display the admin dashboard with statistics and charts.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        // Only allow admin users to access the dashboard
        if ($user->role !== 'admin') {
            return redirect()->route('mitra.dashboard');
        }
        
        // Get dashboard statistics
        $totalSubmissions = PksSubmission::count();
        $approvedSubmissions = PksSubmission::where('status', 'disetujui')->count();
        $pendingSubmissions = PksSubmission::where('status', 'proses')->count();
        // Removed revisionSubmissions count
        $rejectedSubmissions = PksSubmission::where('status', 'ditolak')->count();
        $mitraCount = User::where('role', 'mitra')->count();
        $scheduledMeetingsCount = Rapat::where('status', 'akan_datang')
            ->where('tanggal_waktu', '>=', now())
            ->count();
        
        $statistics = [
            'total' => $totalSubmissions,
            'approved' => $approvedSubmissions,
            'pending' => $pendingSubmissions,
            // Removed 'revision' => $revisionSubmissions,
            'rejected' => $rejectedSubmissions,
            'mitra' => $mitraCount,
            'scheduled_meetings' => $scheduledMeetingsCount
        ];
        
        return Inertia::render('admin/DashboardAdmin', [
            'statistics' => $statistics
        ]);
    }
    
    /**
     * Display the mitra dashboard with submissions or admin management view.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Handle admin users - show management view
        if ($user->role === 'admin') {
            // Build query for all submissions
            $query = PksSubmission::with(['user']);
            
            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%')
                      ->orWhereHas('user', function($userQuery) use ($search) {
                          $userQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            }
            
            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            // Apply date filter
            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }
            
            // Get submissions with pagination and include validity period fields
            $submissions = $query->orderBy('created_at', 'desc')->paginate(10);
            
            // Get dashboard data for admin
            $pendingSubmissions = PksSubmission::where('status', 'proses')->count();
            // Removed revisionSubmissions count
            $mitraCount = User::where('role', 'mitra')->count();
            $scheduledMeetingsCount = Rapat::where('status', 'akan_datang')
                ->where('tanggal_waktu', '>=', now())
                ->count();
            
            return Inertia::render('admin/kelola-pks/Index', [
                'submissions' => $submissions,
                'dashboardData' => [
                    'pending' => $pendingSubmissions,
                    // Removed 'revision' => $revisionSubmissions,
                    'mitra' => $mitraCount,
                    'scheduled_meetings' => $scheduledMeetingsCount,
                ]
            ]);
        }
        
        // Handle mitra users - show their submissions
        $submissions = PksSubmission::with('rapat')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Get upcoming meetings for the mitra
        $upcomingMeetings = Rapat::where(function($query) use ($user) {
                // Case 1: Explicitly invited
                $query->whereHas('invitedMitra', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                // Case 2: Owner of the PKS Submission
                ->orWhereHas('pksSubmission', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                // Case 3: Owner of the MOU Submission
                ->orWhereHas('mou', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            })
            ->where('status', 'akan_datang')
            ->where('tanggal_waktu', '>=', now())
            ->orderBy('tanggal_waktu', 'asc')
            ->get();

        // Append document URLs
        $upcomingMeetings->each(function ($meeting) {
            $meeting->append([
                'pks_document_url',
                'draft_document_url',
                'signed_document_url'
            ]);
        });
            
        // Get statistics for mitra dashboard
        $totalSubmissions = PksSubmission::where('user_id', $user->id)->count();
        $approvedSubmissions = PksSubmission::where('user_id', $user->id)->where('status', 'disetujui')->count();
        $pendingSubmissions = PksSubmission::where('user_id', $user->id)->where('status', 'proses')->count();
        $rejectedSubmissions = PksSubmission::where('user_id', $user->id)->where('status', 'ditolak')->count();
        
        $statistics = [
            'total' => $totalSubmissions,
            'approved' => $approvedSubmissions,
            'pending' => $pendingSubmissions,
            'rejected' => $rejectedSubmissions
        ];

        // Fetch approved MoUs for dropdown
        $approvedMous = \App\Models\Mou::where('user_id', $user->id)
            ->where('status', 'disetujui')
            ->where(function($query) {
                $query->whereNull('validity_period_end')
                      ->orWhere('validity_period_end', '>=', now());
            })
            ->select('id', 'title', 'validity_period_end')
            ->get();

        // Fetch user's recent MoUs for dashboard list
        $mouSubmissions = \App\Models\Mou::with('rapat')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Check if this is a partial reload request for specific data
        if ($request->header('X-Inertia-Partial-Data')) {
            $partialData = explode(',', $request->header('X-Inertia-Partial-Data'));
            
            // If only specific data is requested, ensure we still provide the required structure
            $responseData = [];
            
            if (in_array('submissions', $partialData)) {
                $responseData['submissions'] = $submissions;
            }
            
            if (in_array('statistics', $partialData)) {
                $responseData['statistics'] = $statistics;
            }
            
            if (in_array('upcomingMeetings', $partialData)) {
                $responseData['upcomingMeetings'] = $upcomingMeetings;
            }

            if (in_array('approved_mous', $partialData)) {
                $responseData['approved_mous'] = $approvedMous;
            }

            if (in_array('mou_submissions', $partialData)) {
                $responseData['mou_submissions'] = $mouSubmissions;
            }
            
            // If this is a partial reload but no specific data is requested, 
            // or if submissions/statistics are requested, return the data
            if (!empty($responseData)) {
                return Inertia::render('mitra/Dashboard', $responseData);
            }
        }
        
        // Full render
        return Inertia::render('mitra/Dashboard', [
            'submissions' => $submissions,
            'statistics' => $statistics,
            'upcomingMeetings' => $upcomingMeetings,
            'approved_mous' => $approvedMous,
            'mou_submissions' => $mouSubmissions
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // For mitra users, we'll use a modal on the dashboard
        // For admin users, we'll use the admin create page
        if (Auth::user()->role === 'mitra') {
            return redirect()->route('mitra.dashboard');
        } else {
            return Inertia::render('admin/kelola-pks/Create');
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(PksSubmissionRequest $request)
    {
        $validated = $request->validated();
        
        // Handle document uploads
        if ($request->hasFile('kak_document')) {
            $kakFilePath = $request->file('kak_document')->store('pks-documents', 'public');
            $validated['kak_document_path'] = $kakFilePath;
        }
        
        if ($request->hasFile('mou_document')) {
            $mouFilePath = $request->file('mou_document')->store('pks-documents', 'public');
            $validated['mou_document_path'] = $mouFilePath;
        }
        
        // Set partner name from authenticated user if not provided
        $validated['partner_name'] = $validated['partner_name'] ?? $request->user()->name;
        
        // Map purpose to description for backward compatibility
        $validated['description'] = $validated['purpose'];
        
        // Check if this is an existing PKS being added by admin with approved status
        $isExistingPks = $request->user()->role === 'admin' && $request->input('is_existing_pks') && $request->input('status') === 'disetujui';
        
        // Set the status based on whether it's an existing PKS or new submission
        $status = $isExistingPks ? 'disetujui' : 'proses';
        
        // For existing PKS, use the provided user_id, otherwise use the authenticated user
        $userId = $isExistingPks && isset($validated['user_id']) ? $validated['user_id'] : $request->user()->id;
        
        // Create the submission
        $submission = PksSubmission::create(array_merge($validated, [
            'user_id' => $userId,
            'status' => $status,
        ]));
        
        // Create status history
        StatusHistory::create([
            'pks_submission_id' => $submission->id,
            'status' => $status,
            'notes' => $isExistingPks ? 'PKS yang sudah berjalan sebelum sistem ini dibuat' : 'Dokumen diajukan oleh mitra',
        ]);
        
        // Only send notifications for new submissions, not existing PKS
        if (!$isExistingPks) {
            // Notify admins about the new submission
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PksSubmitted($submission));
                // Send email notification to admins
                $admin->notify(new AdminPksSubmissionNotification($submission));
            }

            //Notify Send email to gmail
            try {
                $gmail = new GmailService();
                $gmail->sendEmail(
                    $request->user()->email,
                    'Pengajuan PKS Diterima',
                    "Halo {$request->user()->name},\n\nPengajuan PKS Anda berhasil dikirim dan sedang diproses.\n\nTerima kasih."
                );
            } catch (\Exception $e) {
                Log::error('Gagal kirim notifikasi Gmail: '.$e->getMessage());
            }
        }
        
        // For Inertia requests, return a proper response
        if ($request->header('X-Inertia')) {
            return back()->with([
                'success' => 'Pengajuan PKS berhasil disimpan.'
            ]);
        }
        
        // Redirect mitra users back to their dashboard instead of admin page
        if ($request->user()->role === 'mitra') {
            return redirect()->route('mitra.dashboard')->with('success', 'Pengajuan PKS berhasil disimpan.');
        } else {
            return redirect()->route('kelola.pks')->with('success', 'Pengajuan PKS berhasil disimpan.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PksSubmission $pksSubmission)
    {
        $pksSubmission->load(['user', 'statusHistories', 'rapat.creator', 'mou']);
        
        return Inertia::render('admin/kelola-pks/Show', [
            'submission' => $pksSubmission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PksSubmission $pksSubmission)
    {
        // Removed revision status check - no longer allow editing
        return redirect()->back()->with('error', 'Dokumen ini tidak dapat diedit.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PksSubmissionRequest $request, PksSubmission $pksSubmission)
    {
        // Ensure Mitra can only update their own submission
        if ($request->user()->role === 'mitra' && $pksSubmission->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validated();
        
        // Update basic fields
        $pksSubmission->title = $validated['title'];
        $pksSubmission->description = $validated['purpose']; // Map purpose to description
        
        // Handle KAK document upload
        if ($request->hasFile('kak_document')) {
            // Delete old file if exists
            if ($pksSubmission->kak_document_path) {
                Storage::disk('public')->delete($pksSubmission->kak_document_path);
            }
            // Store new file
            $path = $request->file('kak_document')->store('pks-documents', 'public');
            $pksSubmission->kak_document_path = $path;
        }
        
        // Handle MoU document upload
        if ($request->hasFile('mou_document')) {
            // Delete old file if exists
            if ($pksSubmission->mou_document_path) {
                Storage::disk('public')->delete($pksSubmission->mou_document_path);
            }
            // Store new file
            $path = $request->file('mou_document')->store('pks-documents', 'public');
            $pksSubmission->mou_document_path = $path;
        }
        
        $pksSubmission->save();
        
        return redirect()->back()->with('success', 'Konten pengajuan PKS berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PksSubmission $pksSubmission)
    {
        $user = $request->user();
        
        // Check if user can delete (only admins or mitra with 'proses' status)
        if ($user->role === 'mitra' && $pksSubmission->status !== 'proses') {
            return redirect()->back()->with('error', 'Hanya pengajuan dengan status "Proses" yang dapat dihapus.');
        }
        
        // Delete associated documents from storage
        if ($pksSubmission->kak_document_path) {
            Storage::disk('public')->delete($pksSubmission->kak_document_path);
        }
        
        if ($pksSubmission->mou_document_path) {
            Storage::disk('public')->delete($pksSubmission->mou_document_path);
        }
        
        if ($pksSubmission->final_document_path) {
            Storage::disk('public')->delete($pksSubmission->final_document_path);
        }
        
        // Delete the submission
        $pksSubmission->delete();
        
        return redirect()->back()->with('success', 'Pengajuan PKS berhasil dihapus.');
    }

    /**
     * Update the status of the specified resource in storage.
     */
    public function updateStatus(Request $request, PksSubmission $pksSubmission)
    {
        Log::info('=== UPDATE STATUS REQUEST ===');
        Log::info('Submission ID: ' . $pksSubmission->id);
        Log::info('Request method: ' . $request->method());
        Log::info('Request data: ', $request->all());
        Log::info('User ID: ' . $request->user()->id);
        Log::info('User role: ' . $request->user()->role);
        
        // Removed 'revisi' from validation rules
        $request->validate([
            'status' => 'required|in:proses,ditolak,disetujui',
            // Removed 'revision_notes' validation
            'final_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'digital_signature' => 'nullable|string',
            'validity_period_start' => 'nullable|date|required_if:status,disetujui',
            'validity_period_end' => 'nullable|date|required_if:status,disetujui|after:validity_period_start',
        ]);
        
        // Check if the submission is already finalized
        if (in_array($pksSubmission->status, ['disetujui', 'ditolak'])) {
            return back()->with('error', 'Pengajuan PKS yang sudah disetujui atau ditolak tidak dapat diubah statusnya.');
        }

        // Store the old status
        $oldStatus = $pksSubmission->status;
        Log::info('Old status: ' . $oldStatus);
        
        $data = [
            'status' => $request->status,
        ];
        
        // Removed revision notes handling
        
        // Handle final document
        if ($request->hasFile('final_document')) {
            if ($pksSubmission->final_document_path) {
                Storage::disk('public')->delete($pksSubmission->final_document_path);
            }
            
            $filePath = $request->file('final_document')->store('pks-final-documents', 'public');
            $data['final_document_path'] = $filePath;
        }
        
        // Handle digital signature and validity period when status is disetujui
        if ($request->status === 'disetujui') {
            if ($request->filled('digital_signature')) {
                $data['digital_signature'] = $request->digital_signature;
                $data['signed_by'] = $request->user()->name;
                $data['signed_at'] = now();
            }
            
            // Add validity period data
            if ($request->filled('validity_period_start') && $request->filled('validity_period_end')) {
                $data['validity_period_start'] = $request->validity_period_start;
                $data['validity_period_end'] = $request->validity_period_end;
            }
        }
        
        Log::info('Updating submission with data: ', $data);
        $result = $pksSubmission->update($data);
        Log::info('Update result: ' . ($result ? 'success' : 'failed'));
        Log::info('Submission updated. New status: ' . $pksSubmission->status);
        
        // Create status history
        StatusHistory::create([
            'pks_submission_id' => $pksSubmission->id,
            'status' => $request->status,
            'notes' => 'Status diperbarui oleh admin',
        ]);
        
        // Notify the mitra about the status update
        $mitra = $pksSubmission->user;
        
        // Send notification based on the new status (removed revision notification)
        $mitra->notify(new PksStatusUpdated($pksSubmission, $oldStatus, $request->status));
        
        Log::info('=== UPDATE STATUS COMPLETED ===');
        
        // For Inertia requests, return a proper response
        if ($request->header('X-Inertia')) {
            return back()->with([
                'success' => 'Status berhasil diperbarui.'
            ]);
        }
        
        // For regular requests, redirect back with success message
        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    /**
     * Get dashboard submissions data for API
     */
    public function getDashboardSubmissions(Request $request)
    {
        $user = $request->user();
        
        // Only allow admin users to access this data
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get query parameters
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        $startDate = $request->query('start_date', '');
        $endDate = $request->query('end_date', '');
        
        // Build query
        $query = PksSubmission::with('user');
        
        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Apply status filter
        if (!empty($status)) {
            $query->where('status', $status);
        }
        
        // Apply date range filter
        if (!empty($startDate)) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if (!empty($endDate)) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        
        // Get submissions ordered by creation date
        $submissions = $query->orderBy('created_at', 'desc')->get();
        
        // Transform the data for the response
        $transformedSubmissions = $submissions->map(function ($submission) {
            return [
                'id' => $submission->id,
                'title' => $submission->title,
                'description' => $submission->description,
                'status' => $submission->status,
                'user' => [
                    'name' => $submission->user->name,
                ],
                'created_at' => $submission->created_at->format('d M Y H:i'),
                'validity_period_start' => $submission->validity_period_start,
                'validity_period_end' => $submission->validity_period_end,
            ];
        });
        
        return response()->json([
            'submissions' => $transformedSubmissions
        ]);
    }

    /**
     * Get dashboard summary data for API
     */
    public function getDashboardSummary(Request $request)
    {
        $user = $request->user();
        
        // Only allow admin users to access this data
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get dashboard statistics
        $totalSubmissions = PksSubmission::count();
        $approvedSubmissions = PksSubmission::where('status', 'disetujui')->count();
        $pendingSubmissions = PksSubmission::where('status', 'proses')->count();
        // Removed revisionSubmissions count
        $rejectedSubmissions = PksSubmission::where('status', 'ditolak')->count();
        $mitraCount = User::where('role', 'mitra')->count();
        $scheduledMeetingsCount = Rapat::where('status', 'akan_datang')
            ->where('tanggal_waktu', '>=', now())
            ->count();
        
        $statistics = [
            'total' => $totalSubmissions,
            'approved' => $approvedSubmissions,
            'pending' => $pendingSubmissions,
            // Removed 'revision' => $revisionSubmissions,
            'rejected' => $rejectedSubmissions,
            'mitra' => $mitraCount,
            'scheduled_meetings' => $scheduledMeetingsCount
        ];
        
        return response()->json([
            'statistics' => $statistics
        ]);
    }
    
    /**
     * Export PKS submissions to PDF
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();
        
        if ($user->role !== 'admin') {
            abort(403);
        }
        
        $query = PksSubmission::with(['user']);
        
        // Use the same filters as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        $submissions = $query->orderBy('created_at', 'desc')->get();
        
        $pdf = Pdf::loadView('pdf.laporan-pks', compact('submissions'))
            ->setPaper('a4', 'landscape');
            
        return $pdf->download('laporan-pks-' . now()->format('Y-m-d') . '.pdf');
    }
    
    /**
     * Get chart data for API
     */
    public function getChartData(Request $request)
    {
        $user = $request->user();
        
        // Only allow admin users to access this data
        if (!$user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Get status distribution
        $statusDistribution = PksSubmission::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Get submissions per month for the last 12 months
        $submissionsPerMonth = PksSubmission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('count(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();
        
        return response()->json([
            'statusDistribution' => $statusDistribution,
            'submissionsPerMonth' => $submissionsPerMonth
        ]);
    }
    
    /**
     * Get notifications for API
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        
        // Get user's notifications
        $notifications = $user->notifications()->orderBy('created_at', 'desc')->get();
        
        return response()->json($notifications);
    }
    
    /**
     * Mark notification as read
     */
    public function markNotificationAsRead(Request $request, $id)
    {
        $user = $request->user();
        
        // Find the notification for this user
        $notification = $user->notifications()->where('id', $id)->first();
        
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }
        
        // Mark as read
        $notification->markAsRead();
        
        return response()->json(['success' => true]);
    }
}