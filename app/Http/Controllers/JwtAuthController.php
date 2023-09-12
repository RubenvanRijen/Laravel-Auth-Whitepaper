<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class JwtAuthController extends Controller
{
    public function __construct()
    {
        // Apply the 'auth:api' middleware to all methods except the beneath
        $this->middleware('auth:api', ['except' => ['login', 'register', 'sendEmailVerification', 'verifyEmail', 'createNewVerificationLink']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        // Validate incoming login request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            // Return validation error if validation fails
            return response()->json(['error' => 'Validation failed', 'data' => $validator->errors()], 422);
        }

        // Attempt to authenticate user
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            // Return error for invalid credentials
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        if (!$user->email_verified_at) {
            // Return error if user's email is not verified
            return response()->json(['error' => 'Email not verified'], 403);
        }

        // Refresh the token and return a new token
        $token = auth()->refresh();
        return $this->createNewToken($token);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request): JsonResponse
    {
        // Validate incoming registration request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            // Return validation error if validation fails
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create a new user with hashed password
        $user = User::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $user->verification_token = Str::random(40);
        $user->save();

        // Return success response with registered user information
        return response()->json(['message' => 'User successfully registered', 'user' => $user], 201);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        // Logout the authenticated user
        auth()->logout();
        return response()->json(['message' => 'User logged out successfully']);
    }

    /**
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        // Refresh the token and return a new token
        $token = auth()->refresh();
        return $this->createNewToken($token);
    }

    /**
     * @return JsonResponse
     */
    public function getCurrentUser(): JsonResponse
    {
        // Get the authenticated user's information
        $user = auth()->user();

        if (!$user) {
            // Return error if user is not authenticated
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Return the user's information
        return response()->json(compact('user'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendEmailVerification(Request $request): JsonResponse
    {
        // Get the authenticated user
        $user = User::where('email', $request->input('email'))->first();

        if ($user->hasVerifiedEmail()) {
            // Return message if email is already verified
            return response()->json(['message' => 'Email is already verified'], 200);
        }

        // Generate the verification link
        $verificationUrl = $this->generateVerificationUrl($user);

        // Return success response
        return response()->json(['url' => $verificationUrl]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createNewVerificationLink(Request $request): JsonResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->verification_token = Str::random(40);
        $user->save();

        // generate a new verification url
        $verificationUrl = $this->generateVerificationUrl($user);

        return response()->json(['url' => $verificationUrl]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyEmail(Request $request): JsonResponse
    {
        // Find the user by the verification token
        $user = User::where('verification_token', $request->verification_token)->first();

        if (!$user) {
            // Return error if the token is invalid
            return response()->json(['message' => 'Invalid verification token'], 400);
        }

        // Mark the user's email as verified and clear the verification token
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        // Return success response
        return response()->json(['message' => 'Email verified successfully']);
    }

    /**
     * @param string $token
     * @return JsonResponse
     */
    protected function createNewToken(string $token): JsonResponse
    {
        // Create a new token response with necessary metadata
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    /**
     * @param User $user
     * @return string
     */
    protected function generateVerificationUrl(User $user): string
    {
        // Generate a signed route URL for email verification
        return URL::temporarySignedRoute(
            'verification.verify', now()->addMinutes(30), ['verification_token' => $user->verification_token]);

    }
}

