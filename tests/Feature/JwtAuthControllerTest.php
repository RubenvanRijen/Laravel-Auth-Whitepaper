<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class JwtAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    // Reset database after each test

    use WithFaker;

    // Use Faker for generating test data

   /**
     * Test user registration.
     *
     * @return void
     */
    public function testUserRegistration()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->json('POST', 'api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User successfully registered',
                'user' => [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);
    }

    /**
     * Test user login.
     *
     * @return void
     */
    public function testUserLogin()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->json('POST', 'api/auth/login', $loginData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    // Include other user attributes
                ],
            ]);
    }

    /**
     * Test token refresh.
     *
     * @return void
     */
    public function testTokenRefresh()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->json('POST', 'api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    // Include other user attributes
                ],
            ]);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testUserLogout()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->json('POST', 'api/auth/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'User logged out successfully']);
    }

    /**
     * Test getting current user profile.
     *
     * @return void
     */
    public function testGetCurrentUserProfile()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->json('GET', 'api/auth/user-profile');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    // Include other user attributes
                ],
            ]);
    }

    /**
     * Test sending email verification.
     *
     * @return void
     */
    public function testSendEmailVerification()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $token = auth()->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->json('POST', 'api/auth/send-verify-email', ['email' => $user->email]);

        $response->assertStatus(200)
            ->assertJsonStructure(['url']);
    }

    /**
     * Test creating a new verification link.
     *
     * @return void
     */
    public function testCreateNewVerificationLink()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->json('POST', 'api/auth/resend-verification', ['email' => $user->email]);

        $response->assertStatus(200)
            ->assertJsonStructure(['url']);
    }

    /**
     * Test verifying email.
     *
     * @return void
     */
    public function testVerifyEmail()
    {
        $user = User::factory()->create(['verification_token' => 'test_token']);
        $response = $this->json('POST', 'api/auth/verify-email/test_token');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email verified successfully']);
    }
}
