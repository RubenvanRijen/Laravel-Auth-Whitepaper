<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class JwtAuthControllerTest extends TestCase
{
    use RefreshDatabase; // Automatically reset the database after each test
    use WithFaker;       // Use Faker for generating fake data

    /**
     * Test user registration.
     */
    public function testUserRegistration()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User successfully registered',
            ]);
    }

    /**
     * Test user login.
     */
    public function testUserLogin()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $credentials = [
            'email' => 'user@example.com',
            'password' => 'password',
        ];

        $response = $this->postJson('api/auth/login', $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user',
            ]);
    }

    /**
     * Test refreshing user's token.
     */
    public function testTokenRefresh()
    {
        $user = User::factory()->create();
        $token = auth()->tokenById($user->id);

        $response = $this->postJson('api/auth/refresh', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user',
            ]);
    }

    /**
     * Test getting the current user's profile.
     */
    public function testGetCurrentUserProfile()
    {
        $user = User::factory()->create();
        $token = auth()->tokenById($user->id);

        $response = $this->getJson('api/auth/user-profile', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    /**
     * Test logging out user.
     */
    public function testUserLogout()
    {
        $user = User::factory()->create();
        $token = auth()->tokenById($user->id);

        $response = $this->postJson('api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User logged out successfully',
            ]);
    }

    /**
     * Test sending email verification link.
     */
    public function testSendEmailVerification()
    {
        $user = User::factory()->create([
            'email' => 'user2@example.com',
        ]);

        $response = $this->postJson('api/auth/send-verify-email', ['email' => $user->email]);

        $response->assertStatus(200);
    }

    /**
     * Test verifying user's email.
     */
    public function testVerifyEmail()
    {
        $user = User::factory()->create([
            'verification_token' => 'test_verification_token',
        ]);

        $response = $this->postJson('api/auth/verify-email/test_verification_token');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Email verified successfully',
            ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'verification_token' => 'test_verification_token',
        ]);
    }

    /**
     * Test creating a new verification link.
     */
    public function testCreateNewVerificationLink()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
        ]);

        $response = $this->postJson('api/auth/resend-verification', ['email' => $user->email]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'url',
            ]);
    }
}