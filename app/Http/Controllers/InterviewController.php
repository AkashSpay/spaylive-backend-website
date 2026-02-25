<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Interview;
use App\Models\Candidate;
use Carbon\Carbon;

class InterviewController extends Controller
{
    /**
     * =====================================
     * Store New Interview
     * =====================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'type' => 'required|in:online,in_person',
            'join_link' => 'nullable|string'
        ]);

        // Auto-generate end_time if not provided
        if (empty($validated['end_time'])) {
            $validated['end_time'] = Carbon::createFromFormat(
                'H:i',
                $validated['start_time']
            )->addHour()->format('H:i');
        }

        // Create Interview
        $interview = Interview::create($validated);

        // Update Candidate status
        Candidate::where('id', $validated['candidate_id'])
            ->update(['status' => 'scheduled']);

        return response()->json([
            'message' => 'Interview scheduled successfully',
            'data' => $interview->load('candidate.position')
        ], 201);
    }

    /**
     * =====================================
     * Get All Interviews
     * =====================================
     */
    public function index(Request $request)
    {
        $query = Interview::with([
            'candidate.position'
        ]);

        // Optional: filter upcoming interviews
        if ($request->boolean('upcoming')) {
            $query->whereDate('date', '>=', now()->toDateString());
        }

        $interviews = $query
            ->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json($interviews);
    }

    /**
     * =====================================
     * Get Single Interview
     * =====================================
     */
    public function show($id)
    {
        $interview = Interview::with([
            'candidate.position'
        ])->findOrFail($id);

        return response()->json($interview);
    }

    /**
     * =====================================
     * Update Interview
     * =====================================
     */
    public function update(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'type' => 'sometimes|in:online,in_person',
            'join_link' => 'nullable|string'
        ]);

        if (isset($validated['start_time']) && empty($validated['end_time'])) {
            $validated['end_time'] = Carbon::createFromFormat(
                'H:i',
                $validated['start_time']
            )->addHour()->format('H:i');
        }

        $interview->update($validated);

        return response()->json([
            'message' => 'Interview updated successfully',
            'data' => $interview->load('candidate.position')
        ]);
    }

    /**
     * =====================================
     * Delete Interview
     * =====================================
     */
    public function destroy($id)
    {
        $interview = Interview::findOrFail($id);
        $interview->delete();

        return response()->json([
            'message' => 'Interview deleted successfully'
        ]);
    }
}