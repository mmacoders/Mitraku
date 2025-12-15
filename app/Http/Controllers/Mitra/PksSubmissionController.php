<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use App\Models\PksSubmission;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PksSubmissionController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, PksSubmission $pksSubmission)
    {
        // Ensure the submission belongs to the authenticated user
        if ($pksSubmission->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to this PKS submission.');
        }
        
        // Load relationships
        $pksSubmission->load(['user', 'statusHistories', 'rapat.creator', 'mou']);
        
        // Append document URLs to rapat if exists
        if ($pksSubmission->rapat) {
            $pksSubmission->rapat->append('pks_document_url');
            $pksSubmission->rapat->append('draft_document_url');
            $pksSubmission->rapat->append('signed_document_url');
        }
        
        // Append document URLs to PKS submission
        $pksSubmission->append('draft_document_url');
        $pksSubmission->append('signed_document_url');
        
        return Inertia::render('mitra/PksSubmissionDetail', [
            'submission' => $pksSubmission,
        ]);
    }
}