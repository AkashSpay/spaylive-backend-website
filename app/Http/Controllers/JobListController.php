<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;
use App\Models\Department;
use App\Models\Position;

class JobListController extends Controller
{

    public function store_department(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $department = Department::create([
            'name' => $request->name,
        ]);
        return response()->json([
            'status' => true,
            'message' => 'department added',
            'department' => $department,
        ], 201);
    }

    public function Department()
    {
        $department = Department::all();
        return response()->json(
            [
                'status' => true,
                'message' => 'department fetched',
                'data' => $department
            ]
        );
    }
    public function Department_delete($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return response()->json([
                'status' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $department->delete();

        return response()->json([
            'status' => true,
            'message' => 'Department deleted successfully'
        ], 200);
    }


    public function store_position(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'job_type' => ['required', 'string', 'max:255'],

            'experience' => ['required', 'string', 'max:100'],
            'salary_range' => ['nullable', 'string', 'max:100'],

            'skills' => ['required', 'array'],
            'skills.*' => ['required', 'string'],

            'responsibility' => ['required', 'array'],
            'responsibility.*' => ['required', 'string'],

            'requirements' => ['required', 'array'],
            'requirements.*' => ['required', 'string'],

            'status' => ['nullable', 'boolean'], // if boolean

            'department_id' => ['required', 'exists:departments,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $position = Position::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'location' => $request->location,
            'job_type' => $request->job_type,
            'experience' => $request->experience,
            'salary_range' => $request->salary_range,
            'skills' => $request->skills,
            'responsibility' => $request->responsibility,
            'requirements' => $request->requirements,
            'status' => $request->status ?? true, // default active
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Position added successfully',
            'position' => $position,
        ], 201);
    }
    public function position()
    {
        $position = Position::all();
        return response()->json(
            [
                'status' => true,
                'message' => 'positions fetched',
                'data' => $position
            ]
        );
    }

    public function show_position($id)
{
    $position = Position::with('department')->find($id); // eager load department

    if (!$position) {
        return response()->json([
            'status' => false,
            'message' => 'Position not found'
        ], 404);
    }

    return response()->json([
        'status' => true,
        'message' => 'Position fetched successfully',
        'data' => $position
    ]);
}

    public function update_position(Request $request, $id)
    {
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'status' => false,
                'message' => 'Position not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'location' => ['sometimes', 'required', 'string', 'max:255'],
            'job_type' => ['sometimes', 'required', 'string', 'max:255'],

            'experience' => ['sometimes', 'required', 'string', 'max:100'],
            'salary_range' => ['nullable', 'string', 'max:100'],

            'skills' => ['sometimes', 'required', 'array'],
            'skills.*' => ['required', 'string'],

            'responsibility' => ['sometimes', 'required', 'array'],
            'responsibility.*' => ['required', 'string'],

            'requirements' => ['sometimes', 'required', 'array'],
            'requirements.*' => ['required', 'string'],

            'status' => ['nullable', 'boolean'],

            'department_id' => ['sometimes', 'required', 'exists:departments,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $position->update($request->only([
            'name',
            'location',
            'job_type',
            'experience',
            'salary_range',
            'skills',
            'responsibility',
            'requirements',
            'status',
            'department_id'
        ]));

        return response()->json([
            'status' => true,
            'message' => 'Position updated successfully',
            'position' => $position->fresh()
        ]);
    }

    public function position_delete($id)
    {
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'status' => false,
                'message' => 'position not found'
            ], 404);
        }

        $position->delete();

        return response()->json([
            'status' => true,
            'message' => 'position deleted successfully'
        ], 200);
    }

    public function dashboard_stats()
    {
        $totalPositions = Position::count();

        $activePositions = Position::where('status', true)->count();

        $inactivePositions = Position::where('status', false)->count();

        $totalDepartments = Department::count();

        return response()->json([
            'status' => true,
            'message' => 'Dashboard statistics fetched successfully',
            'data' => [
                'total_positions' => $totalPositions,
                'active_positions' => $activePositions,
                'inactive_positions' => $inactivePositions,
                'total_departments' => $totalDepartments,
            ]
        ]);
    }

    public function toggle_position_status($id)
    {
        $position = Position::find($id);

        if (!$position) {
            return response()->json([
                'status' => false,
                'message' => 'Position not found'
            ], 404);
        }

        $position->status = !$position->status;
        $position->save();

        return response()->json([
            'status' => true,
            'message' => 'Position status updated',
            'new_status' => $position->status
        ]);
    }

}
