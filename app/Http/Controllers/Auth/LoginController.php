<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Authenticate user and generate token",
     *     description="Login a user and return a token along with user details",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="shreejan@gmail.com"),
     *             @OA\Property(property="password", type="string", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", example="token_string"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=6),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *                 @OA\Property(property="role", type="string", example="patient")
     *             ),
     *             @OA\Property(property="status", type="object",
     *                 @OA\Property(property="message", type="string", example="Patient Login successfully"),
     *                 @OA\Property(property="type", type="string", example="success")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized"),
     *             @OA\Property(property="type", type="string", example="error")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email field is required.")),
     *                 @OA\Property(property="password", type="array", @OA\Items(type="string", example="The password field is required."))
     *             )
     *         )
     *     )
     * )
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
                'status' => [
                    'message' => 'Patient Login successfully',
                    'type' => 'success'
                ],
            ]), 201);
        }

        if ($role === 'doctor') {
            return response()->json(array_merge($data, [
                'status' => [
                    'message' => 'Doctor Login successfully',
                    'type' => 'success'
                ],
            ]), 201);
        }

        if ($role === 'admin') {
            return response()->json(array_merge($data, [
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
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout user",
     *     description="Logout the authenticated user and delete the access token",
     *     tags={"Auth"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logout successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'User logout successfully'], 200);
    }
}
