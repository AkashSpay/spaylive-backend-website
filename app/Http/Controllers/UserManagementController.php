<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /* =====================================
        GET ALL USERS (Search + Role Filter)
    ===================================== */
    public function index(Request $request)
    {
        $query = User::query();

        // Search by name or email
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        // Filter by role
        if ($request->role && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $users
        ]);
    }

    /* =====================================
        CREATE USER
    ===================================== */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:Admin,HR'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /* =====================================
        UPDATE USER
    ===================================== */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully'
        ]);
    }

    /* =====================================
        TOGGLE STATUS
    ===================================== */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User status updated',
            'new_status' => $user->status
        ]);
    }

    /* =====================================
        DELETE USER
    ===================================== */
public function destroy($id)
{
    $user = User::findOrFail($id);

    if ($user->id === auth()->id()) {
        return response()->json([
            'message' => 'You cannot delete your own account.'
        ], 400);
    }

    $user->delete();

    return response()->json([
        'status' => true,
        'message' => 'User deleted successfully'
    ]);
}
}