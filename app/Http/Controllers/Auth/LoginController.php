<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        // Authenticate the user
        $request->authenticate();


        $user = Auth::user();
        $role = $user->role;
        // Generate token for the user
        $token = $user->createToken('Token for ' . $user->email)->plainTextToken;

        // Prepare response data
        $data = [
            'token' => $token,
            'user' => $user,
        ];

        // Determine redirect or response based on user role
        if ($role === 'patient') {
            return response()->json(array_merge($data, [
//                'redirect_url' => route('patient.dashboard'),
                'status' => [
                    'message' => 'Patient Login successfully',
                    'type' => 'success'
                ],
            ]), 201);
        }

        if ($role === 'doctor') {
            return response()->json(array_merge($data, [
//                'redirect_url' => route('doctor.dashboard'),
                'status' => [
                    'message' => 'Doctor Login successfully',
                    'type' => 'success'
                ],
            ]), 201);
        }

        if ($role === 'admin') {
            return response()->json(array_merge($data, [
//                'redirect_url' => route('admin.dashboard'),
                'status' => [
                    'message' => 'Admin Login successfully',
                    'type' => 'success'
                ],
            ]), 201);
        }

        // If role does not match any known roles
        return response()->json([
            'message' => 'Unauthorized',
            'type' => 'error'
        ], 403);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'user logout successfully'], 200);
    }
}
