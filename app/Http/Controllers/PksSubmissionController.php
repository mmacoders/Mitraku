<?php

namespace App\Http\Controllers;

use App\Models\PksSubmission;
use App\Models\StatusHistory;
use App\Models\User;
use App\Http\Requests\PksSubmissionRequest;
use App\Notifications\PksSubmitted;
use App\Notifications\PksStatusUpdated;
use App\Notifications\PksRevisionRequested;
use App\Notifications\AdminPksSubmissionNotification; // Added this line
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Services\GmailService;
use Illuminate\Support\Facades\Log;
use App\Models\Rapat;

class PksSubmissionController extends Controller
{
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
            
            // Apply date range filter
            if ($request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }
            
            // Get submissions with user relationship
            $submissions = $query->latest()->paginate(10);
            
            // Get statistics
            $totalSubmissions = PksSubmission::count();
            $approvedSubmissions = PksSubmission::where('status', 'disetujui')->count();
            $pendingSubmissions = PksSubmission::where('status', 'proses')->count();
            $rejectedSubmissions = PksSubmission::where('status', 'ditolak')->count();
            $revisionSubmissions = PksSubmission::where('status', 'revisi')->count();
            
            return Inertia::render('admin/kelola-pks/Index', [
                'submissions' => $submissions,
                'statistics' => [
                    'total' => $totalSubmissions,
                    'approved' => $approvedSubmissions,
                    'pending' => $pendingSubmissions,
                    'rejected' => $rejectedSubmissions,
                    'revision' => $revisionSubmissions,
                ],
            ]);
        }
        
        // Only allow mitra users to access the dashboard view
        if ($user->role !== 'mitra') {
            return redirect()->route('dashboard');
        }
        
        // Build query for user's submissions
        $query = PksSubmission::where('user_id', $user->id);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply date range filter
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        
        // Get submissions with user relationship
        $submissions = $query->with(['user'])->latest()->paginate(10);
        
        // Get statistics
        $totalSubmissions = PksSubmission::where('user_id', $user->id)->count();
        $approvedSubmissions = PksSubmission::where('user_id', $user->id)->where('status', 'disetujui')->count();
        $pendingSubmissions = PksSubmission::where('user_id', $user->id)->where('status', 'proses')->count();
        $rejectedSubmissions = PksSubmission::where('user_id', $user->id)->where('status', 'ditolak')->count();
        
        return Inertia::render('mitra/Dashboard', [
            'submissions' => $submissions,
            'statistics' => [
                'total' => $totalSubmissions,
                'approved' => $approvedSubmissions,
                'pending' => $pendingSubmissions,
                'rejected' => $rejectedSubmissions,
            ],
        ]);
    }
    
    /**
     * Display the admin dashboard with statistics.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        // Only allow admin users to access this dashboard
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Get statistics
        $totalSubmissions = PksSubmission::count();
        $approvedSubmissions = PksSubmission::where('status', 'disetujui')->count();
        $rejectedSubmissions = PksSubmission::where('status', 'ditolak')->count();
        $pendingSubmissions = PksSubmission::where('status', 'proses')->count();
        $revisionSubmissions = PksSubmission::where('status', 'revisi')->count();
        
        // Get recent submissions
        $recentSubmissions = PksSubmission::with('user')->latest()->take(5)->get();
        
        // Get recent notifications/activities
        $recentActivities = $user->notifications()->latest()->take(4)->get();
        
        // Get mitra count
        $mitraCount = User::where('role', 'mitra')->count();
        
        // Get scheduled meetings count (meetings with status 'akan_datang')
        // This counts ALL meetings with status 'akan_datang', regardless of date
        $scheduledMeetingsCount = Rapat::where('status', 'akan_datang')->count();
        
        return Inertia::render('admin/DashboardAdmin', [
            'statistics' => [
                'total' => $totalSubmissions,
                'approved' => $approvedSubmissions,
                'rejected' => $rejectedSubmissions,
                'pending' => $pendingSubmissions,
                'revision' => $revisionSubmissions,
                'mitra' => $mitraCount,
                'scheduled_meetings' => $scheduledMeetingsCount,
            ],
            'recentSubmissions' => $recentSubmissions,
            'recentActivities' => $recentActivities,
        ]);
    }
    
    /**
     * Display the simplified dashboard with statistics.
     */
    public function simplifiedDashboard(Request $request)
    {
        $user = $request->user();
        
        // Only allow admin users to access this dashboard
        if (!$user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        
        // Get statistics
        $totalSubmissions = PksSubmission::count();
        $approvedSubmissions = PksSubmission::where('status', 'disetujui')->count();
        $rejectedSubmissions = PksSubmission::where('status', 'ditolak')->count();
        $pendingSubmissions = PksSubmission::where('status', 'proses')->count();
        $revisionSubmissions = PksSubmission::where('status', 'revisi')->count();
        
        return Inertia::render('SimplifiedDashboard', [
            'statistics' => [
                'total' => $totalSubmissions,
                'approved' => $approvedSubmissions,
                'rejected' => $rejectedSubmissions,
                'pending' => $pendingSubmissions,
                'revision' => $revisionSubmissions,
            ]
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
        
        // Get statistics
        $totalSubmissions = PksSubmission::count();
        $approvedSubmissions = PksSubmission::where('status', 'disetujui')->count();
        $rejectedSubmissions = PksSubmission::where('status', 'ditolak')->count();
        $pendingSubmissions = PksSubmission::where('status', 'proses')->count();
        $revisionSubmissions = PksSubmission::where('status', 'revisi')->count();
        
        // Get mitra count
        $mitraCount = User::where('role', 'mitra')->count();
        
        // Get scheduled meetings count (meetings with status 'akan_datang')
        $scheduledMeetingsCount = Rapat::where('status', 'akan_datang')->count();
        
        return response()->json([
            'statistics' => [
                'total' => $totalSubmissions,
                'approved' => $approvedSubmissions,
                'rejected' => $rejectedSubmissions,
                'pending' => $pendingSubmissions,
                'revision' => $revisionSubmissions,
                'mitra' => $mitraCount,
                'scheduled_meetings' => $scheduledMeetingsCount,
            ]
        ]);
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
            $query->where('created_at', '>=', $startDate);
        }
        
        if (!empty($endDate)) {
            $query->where('created_at', '<=', $endDate);
        }
        
        // Get submissions
        $submissions = $query->latest()->paginate(10);
        
        return response()->json($submissions);
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
        
        // Get status distribution for donut chart
        $statusDistribution = PksSubmission::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Get submissions per month for line chart
        $submissionsPerMonth = PksSubmission::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/kelola-pks/Create');
    }

    /**
     * Store a newly created resource in storage. And Gmail service
     */
    public function store(PksSubmissionRequest $request, GmailService $gmail)
    {
        $validated = $request->validated();
        
        $submission = new PksSubmission($validated);
        $submission->user_id = $request->user()->id;
        $submission->status = 'proses';
        
        // Set partner name from authenticated user if not provided
        $submission->partner_name = $validated['partner_name'] ?? $request->user()->name;
        
        // Map purpose to description for backward compatibility
        $submission->description = $validated['purpose'];
        
        // Handle KAK document upload
        if ($request->hasFile('kak_document')) {
            $kakFilePath = $request->file('kak_document')->store('pks-documents', 'public');
            $submission->kak_document_path = $kakFilePath;
        }
        
        // Handle MoU document upload
        if ($request->hasFile('mou_document')) {
            $mouFilePath = $request->file('mou_document')->store('pks-documents', 'public');
            $submission->mou_document_path = $mouFilePath;
        }
        
        $submission->save();
        
        // Create initial status history
        StatusHistory::create([
            'pks_submission_id' => $submission->id,
            'status' => 'proses',
            'notes' => 'Dokumen diajukan oleh mitra',
        ]);
        
        // Notify admins about the new submission
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new PksSubmitted($submission));
            // Send email notification to admins
            $admin->notify(new AdminPksSubmissionNotification($submission));
        }

        //Notify Send email to gmail
        try {
            $gmail->sendEmail(
                $request->user()->email,
                'Pengajuan PKS Diterima',
                "Halo {$request->user()->name},\n\nPengajuan PKS Anda berhasil dikirim dan sedang diproses.\n\nTerima kasih."
            );
        } catch (\Exception $e) {
            Log::error('Gagal kirim notifikasi Gmail: '.$e->getMessage());
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
        $pksSubmission->load(['user', 'statusHistories', 'rapat.creator']);
        
        return Inertia::render('admin/kelola-pks/Show', [
            'submission' => $pksSubmission,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PksSubmission $pksSubmission)
    {
        // Only allow editing if status is 'revisi'
        if ($pksSubmission->status !== 'revisi') {
            return redirect()->back()->with('error', 'Dokumen ini tidak dapat direvisi.');
        }
        
        return Inertia::render('admin/kelola-pks/Edit', [
            'submission' => $pksSubmission,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PksSubmissionRequest $request, PksSubmission $pksSubmission)
    {
        // Only allow updating if status is 'revisi'
        if ($pksSubmission->status !== 'revisi') {
            return redirect()->back()->with('error', 'Dokumen ini tidak dapat direvisi.');
        }
        
        $validated = $request->validated();
        
        // Handle document uploads if provided
        if ($request->hasFile('kak_document')) {
            // Delete old document if exists
            if ($pksSubmission->kak_document_path) {
                Storage::disk('public')->delete($pksSubmission->kak_document_path);
            }
            
            $kakFilePath = $request->file('kak_document')->store('pks-documents', 'public');
            $validated['kak_document_path'] = $kakFilePath;
        }
        
        if ($request->hasFile('mou_document')) {
            // Delete old document if exists
            if ($pksSubmission->mou_document_path) {
                Storage::disk('public')->delete($pksSubmission->mou_document_path);
            }
            
            $mouFilePath = $request->file('mou_document')->store('pks-documents', 'public');
            $validated['mou_document_path'] = $mouFilePath;
        }
        
        // Set partner name from authenticated user if not provided
        $validated['partner_name'] = $validated['partner_name'] ?? $request->user()->name;
        
        // Map purpose to description for backward compatibility
        $validated['description'] = $validated['purpose'];
        
        $pksSubmission->update($validated);

        return redirect()->route('mitra.dashboard')->with('success', 'Pengajuan PKS berhasil diperbarui.');
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
        
        $request->validate([
            'status' => 'required|in:proses,revisi,ditolak,disetujui',
            'revision_notes' => 'nullable|string',
            'final_document' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'digital_signature' => 'nullable|string',
            'validity_period_start' => 'nullable|date|required_if:status,disetujui',
            'validity_period_end' => 'nullable|date|required_if:status,disetujui|after:validity_period_start',
        ]);
        
        // Store the old status
        $oldStatus = $pksSubmission->status;
        Log::info('Old status: ' . $oldStatus);
        
        $data = [
            'status' => $request->status,
        ];
        
        // Handle revision notes
        if ($request->filled('revision_notes')) {
            $data['revision_notes'] = $request->revision_notes;
        }
        
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
            'notes' => $request->revision_notes ?? 'Status diperbarui oleh admin',
        ]);
        
        // Notify the mitra about the status update
        $mitra = $pksSubmission->user;
        
        // Send different notifications based on the new status
        if ($request->status === 'revisi' && $request->filled('revision_notes')) {
            $mitra->notify(new PksRevisionRequested($pksSubmission, $request->revision_notes));
        } else {
            $mitra->notify(new PksStatusUpdated($pksSubmission, $oldStatus, $request->status));
        }
        
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, PksSubmission $pksSubmission)
    {
        // Authorization check: Only the owner or admin can delete
        if ($request->user()->id !== $pksSubmission->user_id && !$request->user()->isAdmin()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk menghapus pengajuan ini.');
        }
        
        // Delete documents if they exist
        if ($pksSubmission->document_path) {
            Storage::disk('public')->delete($pksSubmission->document_path);
        }
        
        if ($pksSubmission->kak_document_path) {
            Storage::disk('public')->delete($pksSubmission->kak_document_path);
        }
        
        if ($pksSubmission->mou_document_path) {
            Storage::disk('public')->delete($pksSubmission->mou_document_path);
        }
        
        if ($pksSubmission->final_document_path) {
            Storage::disk('public')->delete($pksSubmission->final_document_path);
        }
        
        $pksSubmission->delete();
        
        // Redirect based on user role
        if ($request->user()->role === 'mitra') {
            return redirect()->route('mitra.dashboard')->with('success', 'Pengajuan PKS berhasil dihapus.');
        } else {
            return redirect()->route('kelola.pks')->with('success', 'Pengajuan PKS berhasil dihapus.');
        }
    }
    
    /**
     * Get user notifications
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->take(20)->get();
        
        // Filter out notifications for PKS submissions that no longer exist
        $filteredNotifications = $notifications->filter(function ($notification) {
            // Check if this is a PKS-related notification
            if (in_array($notification->type, [
                'App\Notifications\PksSubmitted',
                'App\Notifications\PksStatusUpdated',
                'App\Notifications\PksRevisionRequested',
                'App\Notifications\AdminPksSubmissionNotification'
            ])) {
                // Extract PKS submission ID from notification data
                $pksId = $notification->data['pks_submission_id'] ?? null;
                if ($pksId) {
                    // Check if the PKS submission still exists
                    return PksSubmission::where('id', $pksId)->exists();
                }
            }
            // For non-PKS notifications or notifications without PKS ID, keep them
            return true;
        });
        
        return response()->json($filteredNotifications->values());
    }
    
    /**
     * Mark a notification as read
     */
    public function markNotificationAsRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->first();
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false]);
    }
}