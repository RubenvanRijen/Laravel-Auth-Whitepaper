<?php

namespace App\Http\Documentation;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Roles",
 *     description="Endpoints for Roles"
 * )
 */
interface RoleControllerDocumentation
{

    /**
     * @OA\Get(
     *     path="/api/roles",
     *     summary="Get a list of all roles",
     *     tags={"Roles"},
     *     security={{"jwt_token":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of roles",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", format="int64", description="Role ID"),
     *                 @OA\Property(property="title", type="string", description="Role title"),
     *                 @OA\Property(property="description", type="string", description="Role description"),
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     * )
     */
    public function index(): JsonResponse;


    /**
     * @OA\Post(
     *     path="/api/roles",
     *     summary="Create a new role",
     *     tags={"Roles"},
     *     security={{"jwt_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Role data",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="Role title"),
     *             @OA\Property(property="description", type="string", description="Role description"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Role created successfully",
     *          @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", format="int64", description="Role ID"),
     *                 @OA\Property(property="title", type="string", description="Role title"),
     *                 @OA\Property(property="description", type="string", description="Role description"),
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No permissions",
     *     ),
     * )
     */
    public function store(Request $request): JsonResponse;


    /**
     * @OA\Get(
     *     path="/api/roles/{id}",
     *     summary="Get a specific role by ID",
     *     tags={"Roles"},
     *     security={{"jwt_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role details",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", format="int64", description="Role ID"),
     *                 @OA\Property(property="title", type="string", description="Role title"),
     *                 @OA\Property(property="description", type="string", description="Role description"),
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No permissions",
     *     ),
     * )
     */
    public function show(mixed $id): JsonResponse;

    /**
     * @OA\Put(
     *     path="/api/roles/{id}",
     *     summary="Update an existing role by ID",
     *     tags={"Roles"},
     *     security={{"jwt_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated role data",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", description="Role title"),
     *             @OA\Property(property="description", type="string", description="Role description"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Role updated successfully",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", format="int64", description="Role ID"),
     *                 @OA\Property(property="title", type="string", description="Role title"),
     *                 @OA\Property(property="description", type="string", description="Role description"),
     *             )
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No permissions",
     *     ),
     * )
     */
    public function update(Request $request, mixed $id): JsonResponse;


    /**
     * @OA\Delete(
     *     path="/api/roles/{id}",
     *     summary="Delete an existing role by ID",
     *     tags={"Roles"},
     *     security={{"jwt_token":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Role ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Role deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No permissions",
     *     ),
     * )
     */
    public function destroy(mixed $id): JsonResponse;
}
