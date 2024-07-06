<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *    title="Laravel Authentication Api",
 *    description="Laravel Authentication Api",
 *    version="1.0.0",
 * ) 
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @OA\SecurityScheme(
     *     type="http",
     *     description="JWT Token",
     *     name="Authorization",
     *     in="header",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     securityScheme="jwt_token",
     * )
     */
    public function documentation()
    {
    }
}
