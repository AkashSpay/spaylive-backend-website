<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Validator;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
        'password' => ['required', 'confirmed'],
        'address' => ['required','string'],
        'phone' => ['required'],
        'company_name' => ['required'],
        'business_type' => ['required'],
        'city' => ['required'],
        'state' => ['required'],
        'pincode' => ['required'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422);
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'address' => $request->address,
        'phone' => $request->phone,
        'company_name' => $request->company_name,
        'business_type' => $request->business_type,
        'city' => $request->city,
        'state' => $request->state,
        'pincode' => $request->pincode,
        'role' => 'user',
    ]);

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'status' => true,
        'message' => 'Registration successful',
        'token' => $token,
        'user' => $user,
    ], 201);
}



}
