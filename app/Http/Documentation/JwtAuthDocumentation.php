<?php

namespace App\Http\Documentation;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Endpoints for user authentication and authorization"
 * )
 * the interace for the JwtAuthController. Store the documentation here.
 */
interface JwtAuthDocumentation
{

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "password"},
     *                 @OA\Property(property="email", type="string", format="email", example="admin@example.com", description="Email address"),
     *                 @OA\Property(property="password", type="string", format="password", example="password", description="Password"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged in",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Successfully logged in"),
     *             @OA\Property(property="token", type="string", example="your-access-token"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid credentials"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Email not verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Email not verified"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="data", type="object", example={"email": "The email field is required.", "password": "The password field is required."}),
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="User registration",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "email", "password", "password_confirmation"},
     *                 @OA\Property(property="name", type="string", example="John Doe", description="User's name"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Email address"),
     *                 @OA\Property(property="password", type="string", format="password", example="password123", description="Password"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="Password confirmation"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully registered"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-04T12:34:56Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-04T12:34:56Z"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object", example={"name": "The name field is required.", "email": "The email field is required."}),
     *         )
     *     ),
     * )
     */
    public function register(Request $request): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged out successfully"),
     *         )
     *     ),
     * )
     */
    public function logout(): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     summary="Refresh authentication token",
     *     tags={"Authentication"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="token", type="string", example="new-access-token"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated"),
     *         )
     *     ),
     * )
     */
    public function refresh(): JsonResponse;

    /**
     * @OA\Get(
     *     path="/api/auth/user-profile",
     *     summary="Get current user information",
     *     tags={"Authentication"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current user information",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2023-10-04T12:34:56Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2023-10-04T12:34:56Z"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not authenticated"),
     *         )
     *     ),
     * )
     */
    public function getCurrentUser(): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/auth/send-email-verification",
     *     summary="Send email verification link",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"email", "redirect_url"},
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com", description="Email address"),
     *                 @OA\Property(property="redirect_url", type="string", example="https://example.com/verify-email", description="Verification URL to redirect to after verification"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verification link sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="https://example.com/verify-email?verification_token=your-token&redirect_url=https://example.com/verify-email"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Email has already been verified",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email has already been verified"),
     *         )
     *     ),
     * )
     */
    public function sendEmailVerification(Request $request): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/auth/create-new-verification-link",
     *     summary="Create a new email verification link",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"verification_token", "redirect_url"},
     *                 @OA\Property(property="verification_token", type="string", example="your-verification-token", description="Verification token"),
     *                 @OA\Property(property="redirect_url", type="string", example="https://example.com/verify-email", description="Verification URL to redirect to after verification"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="New email verification link created and sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="url", type="string", example="https://example.com/verify-email?verification_token=new-token&redirect_url=https://example.com/verify-email"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User not found"),
     *         )
     *     ),
     * )
     */
    public function createNewVerificationLink(Request $request): JsonResponse;

    /**
     * @OA\Post(
     *     path="/api/auth/verify-email",
     *     summary="Verify user email",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"verification_token", "redirect_url"},
     *                 @OA\Property(property="verification_token", type="string", example="your-verification-token", description="Verification token"),
     *                 @OA\Property(property="redirect_url", type="string", example="https://example.com/verify-email", description="Verification URL to redirect to after verification"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Email verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email verified successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid verification token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid verification token"),
     *         )
     *     ),
     * )
     */
    public function verifyEmail(Request $request);

    /**
     * @OA\Post(
     *     path="/api/auth/createUser",
     *     summary="Create a user for an admin",
     *     operationId="createUser",
     *     tags={"Authentication"},
     *     security={{ "jwt_token": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
     *             @OA\Property(property="role", type="string", example="admin"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User successfully registered"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The role has not been found")
     *         )
     *     ),
     * )
     */
    public function createUser(Request $request): JsonResponse;
}