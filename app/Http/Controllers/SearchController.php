<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Position;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q');
        $limit = $request->get('limit', 8); // Default 8 results
        
        if (!$query || strlen($query) < 2) {
            return response()->json([
                'status' => true,
                'data' => []
            ]);
        }

        $searchTerm = '%' . $query . '%';

        // Search candidates
        $candidates = Candidate::with('position: id,name')
            ->where('name', 'LIKE', $searchTerm)
            ->orWhere('email', 'LIKE', $searchTerm)
            ->limit($limit)
            ->get()
            ->map(function($candidate) {
                return [
                    'id' => $candidate->id,
                    'type' => 'candidate',
                    'name' => $candidate->name,
                    'email' => $candidate->email,
                    'position' => $candidate->position?->name,
                    'status' => $candidate->status,
                    'created_at' => $candidate->created_at,
                    'url' => "/admin/candidates/{$candidate->id}"
                ];
            });

        // Search positions
        $positions = Position::with('department:id,name')
            ->where('name', 'LIKE', $searchTerm)
            ->limit($limit)
            ->get()
            ->map(function($position) {
                return [
                    'id' => $position->id,
                    'type' => 'position',
                    'name' => $position->name,
                    'department' => $position->department?->name,
                    'status' => $position->status,
                    'location' => $position->location,
                    'created_at' => $position->created_at,
                    'url' => "/admin/positions/{$position->id}"
                ];
            });

        // Combine and sort results (newest first)
        $results = $candidates->concat($positions)
            ->sortByDesc('created_at')
            ->values()
            ->take($limit);

        return response()->json([
            'status' => true,
            'data' => $results,
            'meta' => [
                'query' => $query,
                'total' => $results->count()
            ]
        ]);
    }
}