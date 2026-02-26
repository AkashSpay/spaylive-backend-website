<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Candidate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\CandidateTemplateMail;


class CandidateController extends Controller
{
    /* =====================================
        APPLY (Public API)
    ===================================== */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile_no' => [
                'required',
                'string',
                'regex:/^\+?[0-9]{10,15}$/',
            ],
            'location' => 'required|string|max:255',
            'position_id' => 'required|exists:positions,id',
            'resume' => 'required|mimes:pdf,doc,docx|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Prevent duplicate applications
        $existing = Candidate::where('email', $request->email)
            ->where('position_id', $request->position_id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => false,
                'message' => 'You have already applied for this position.'
            ], 409);
        }

        // 1️⃣ Create candidate FIRST (resume null temporarily)
        $candidate = Candidate::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_no' => $request->mobile_no,
            'location' => $request->location,
            'position_id' => $request->position_id,
            'status' => 'pending',
            'resume' => null
        ]);

        // 2️⃣ Store resume using candidate ID
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $original = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $ext = $file->getClientOriginalExtension();

            // Clean filename (remove spaces & special chars)
            $original = preg_replace('/[^A-Za-z0-9\-]/', '_', $original);

            $filename = $original . '_' . $candidate->id . '.' . $ext;

            $path = $file->storeAs('resumes', $filename, 'public');

            // 3️⃣ Update resume column
            $candidate->update([
                'resume' => $path
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Application submitted successfully',
            'data' => $candidate
        ], 201);
    }

    /* =====================================
        ADMIN: Get Candidates (Filter/Search)
    ===================================== */

    public function getCandidates(Request $request)
    {
        $query = Candidate::with('position');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $candidates = $query->latest()->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $candidates
        ]);
    }

    /* =====================================
        Schedule Interview
    ===================================== */

    public function schedule(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'interview_date' => 'required|date',
            'interview_time' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $candidate = Candidate::findOrFail($id);

        $dateTime = $request->interview_date . ' ' . $request->interview_time;

        // Update candidate
        $candidate->update([
            'interview_at' => $dateTime,
            'status' => 'scheduled'
        ]);

        // Send email notification
        try {
            Mail::raw(
                "Hello {$candidate->name},\n\nYour interview has been scheduled on {$dateTime}.\n\nBest regards,\nCompany HR",
                function ($mail) use ($candidate) {
                    $mail->to($candidate->email)
                        ->subject('Interview Scheduled');
                }
            );
        } catch (\Exception $e) {
            // Optional: log the error if email fails
            Log::error("Failed to send interview email: " . $e->getMessage());
        }

        return response()->json([
            'status' => true,
            'message' => 'Interview scheduled successfully and email sent to candidate'
        ]);
    }
    /* =====================================
        Accept Candidate
    ===================================== */

    public function accept($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update(['status' => 'accepted']);

        return response()->json([
            'status' => true,
            'message' => 'Candidate accepted'
        ]);
    }

    /* =====================================
        Reject Candidate
    ===================================== */

    public function reject($id)
    {
        $candidate = Candidate::findOrFail($id);
        $candidate->update(['status' => 'rejected']);

        return response()->json([
            'status' => true,
            'message' => 'Candidate rejected'
        ]);
    }

    /* =====================================
        Bulk Reject
    ===================================== */

    public function bulkReject(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Candidate::whereIn('id', $request->ids)
            ->update(['status' => 'rejected']);

        return response()->json([
            'status' => true,
            'message' => 'Selected candidates rejected'
        ]);
    }

    /* =====================================
        Send Custom Email
    ===================================== */


public function sendEmail(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'subject' => 'required',
        'message' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $candidate = Candidate::findOrFail($id);

    Mail::to($candidate->email)
        ->send(new CandidateTemplateMail(
            $candidate,
            $request->subject,
            $request->message
        ));

    return response()->json([
        'status' => true,
        'message' => 'Email sent successfully using template'
    ]);
}

    /* =====================================
        Download Resume
    ===================================== */

    public function downloadResume($id)
    {
        $candidate = Candidate::findOrFail($id);

        // Full path to file
        $filePath = storage_path('app/public/' . $candidate->resume);

        if (!file_exists($filePath)) {
            return response()->json([
                'status' => false,
                'message' => 'File not found'
            ], 404);
        }

        // Use original filename in download (optional, nicer for user)
        $originalName = pathinfo($candidate->resume, PATHINFO_BASENAME);

        return response()->download($filePath, $originalName);
    }



    /* =====================================
     Preview Resume
 ===================================== */

    public function previewResume($id)
    {
        $candidate = Candidate::findOrFail($id);

        $filePath = storage_path('app/public/' . $candidate->resume);

        if (!file_exists($filePath)) {
            return response()->json([
                'status' => false,
                'message' => 'File not found'
            ], 404);
        }

        // Get the file content
        $fileContent = file_get_contents($filePath);
        $mimeType = mime_content_type($filePath);

        // Return the file with Content-Disposition: inline to force display in browser
        return response($fileContent, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($candidate->resume) . '"');
    }
}