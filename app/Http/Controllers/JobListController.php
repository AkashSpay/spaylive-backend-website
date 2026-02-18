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

    public function store_department(Request $request){

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

    public function Department(){
      $department = Department::all();
      return response()->json(
        [
            'status' => true,
            'message' => 'department fetched',
            'data' => $department]
      );
    }  
   public function Department_delete($id){
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
        'responsibility' => ['required', 'array'],
        'responsibility.*' => ['required', 'string'],

        'requirements' => ['required', 'array'],
        'requirements.*' => ['required', 'string'],
        'department_id' => ['required', 'exists:departments,id'],  // ✅ correct
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422);
    }

    $position = Position::create([
        'department_id' => $request->department_id,  // ✅ foreign key
        'name' => $request->name,
        'location' => $request->location,
        'job_type' => $request->job_type,
        'responsibility' => $request->responsibility,
        'requirements' => $request->requirements,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Position added successfully',
        'position' => $position,    
    ], 201);
}
    public function position(){
      $position = Position::all();
      return response()->json(
        [
            'status' => true,
            'message' => 'positions fetched',
            'data' => $position
            ]
      );
    } 
 public function position_delete($id){
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

}
