<?php

use App\Http\Controllers\JwtAuthController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return view('welcome');
//});

// authentication routes
Route::prefix('auth')->group(
    function ($router) {
        Route::post('/login', [JwtAuthController::class, 'login']);
        Route::post('/register', [JwtAuthController::class, 'register']);
        Route::post('/createUser', [JwtAuthController::class, 'createUser'])->middleware(['jwt_full', 'admin']);


        Route::post('/logout', [JwtAuthController::class, 'logout'])->middleware('jwt_full');
        Route::get('/user-profile', [JwtAuthController::class, 'getCurrentUser'])->middleware('jwt_full');
        Route::post('/refresh', [JwtAuthController::class, 'refresh'])->middleware('jwt_basic');

        Route::post('/send-verify-email', [JwtAuthController::class, 'sendEmailVerification']);
        Route::post('/resend-verification', [JwtAuthController::class, 'createNewVerificationLink']);
        // set the where cause otherwise there kept being a problem with the url not being well seen by laravel.
        // now laravel properly sees the verification_token and the redirect_url and makes it one good route.
        Route::get('/verify-email/{verification_token}/{redirect_url}', [JwtAuthController::class, 'verifyEmail'])->name('verification.verify.api')->where('redirect_url', '.*');
    }
);


Route::prefix('roles')->group(
    function ($router) {
        // Route to get a list of all roles
        Route::get('/', [RoleController::class, 'index'])->middleware(['jwt_full', 'admin']);
        // Route to get a specific role by ID
        Route::get('/{id}', [RoleController::class, 'show'])->middleware(['jwt_full', 'admin']);
        // Route to create a new role
        Route::post('/', [RoleController::class, 'store'])->middleware(['jwt_full', 'admin']);
        // Route to update an existing role by ID
        Route::put('/{id}', [RoleController::class, 'update'])->middleware(['jwt_full', 'admin']);
        // Route to delete an existing role by ID
        Route::delete('/{id}', [RoleController::class, 'destroy'])->middleware(['jwt_full', 'admin']);
    }
);
