<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RolesTableSeeder;
use Illuminate\Support\Facades\Artisan;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Cookie;

class JwtAuthControllerTest extends TestCase
{
    use RefreshDatabase; // Automatically reset the database after each test
    use WithFaker;       // Use Faker for generating fake data
    protected $adminHeaders = [];


    /**
     * Call before the tests are run. Put the necessary data in.
     */
    protected function setUp(): void
    {
        parent::setUp();
        // Migrate the database
        Artisan::call('migrate');
        // set the authentication for certain specific routes.
        Artisan::call('db:seed', ['--class' => RolesTableSeeder::class]);
        $this->createAdminUser();
    }

    protected function createAdminUser()
    {
        $user = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($adminRole);
        $token = JWTAuth::fromUser($user);
        $headers = ['Authorization' => 'Bearer ' . $token];
        $this->setAdminHeaders($headers);
    }

    protected function setAdminHeaders(array $headers)
    {
        $this->adminHeaders = $headers;
    }

    /** @test */
    public function testLogin()
    {
        // Create a user for testing
        $user = Factory::factoryForModel(User::class)->create([
            'password' => bcrypt('password123'),
        ]);

        // Define the login data
        $data = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        // Send a POST request to the login route
        $response = $this->post('/api/auth/login', $data);

        // Assert that the response status is 200 (or the appropriate status code)
        $response->assertStatus(200);

        // Assert that the response contains the 'access_token' key
        $response->assertJsonStructure(['access_token']);
    }

    /** @test */
    public function testRegister()
    {
        // Define the registration data
        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Send a POST request to the register route
        $response = $this->post('/api/auth/register', $data);

        // Assert that the response status is 201 (or the appropriate status code)
        $response->assertStatus(201);

        // Assert that the response contains the 'message' key
        $response->assertJsonStructure(['message']);
    }

    /** @test */
    public function testLogout()
    {
        // Create a user for testing
        $user = Factory::factoryForModel(User::class)->create();

        // Authenticate the user
        $token = auth('api')->login($user);

        // Send a POST request to the logout route with the JWT token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/auth/logout');

        // Assert that the response status is 200 (or the appropriate status code)
        $response->assertStatus(200);

        // Alternatively, you can assert that the cookie value is empty
        $this->assertEmpty(Cookie::get('jwt_token'));
    }

    /** @test */
    public function testSendEmailVerification()
    {
        // Create a user for testing
        $user = Factory::factoryForModel(User::class)->create([
            'verification_token' => 'old_verification_token',
            'email_verified_at' => null
        ]);

        // Define the email verification data
        $data = [
            'email' => $user->email,
            'redirect_url' => 'http://example.com/verification-success',
        ];

        // Send a POST request to the send-verify-email route
        $response = $this->post('api/auth/send-verify-email', $data);

        // Assert that the response status is 200 (or the appropriate status code)
        $response->assertStatus(200);

        // Assert that the response contains the 'url' key
        $response->assertJsonStructure(['url']);
    }

    /** @test */
    public function testCreateNewVerificationLink()
    {
        // Create a user for testing
        $user = Factory::factoryForModel(User::class)->create([
            'verification_token' => 'old_verification_token',
            'email_verified_at' => null
        ]);

        // Define the data for creating a new verification link
        $data = [
            'verification_token' => 'old_verification_token',
            'redirect_url' => 'http://example.com/verification-success',
        ];

        // Send a POST request to the resend-verification route
        $response = $this->post('api/auth/resend-verification', $data);

        // Assert that the response status is 200 (or the appropriate status code)
        $response->assertStatus(200);

        // Assert that the response contains the 'url' key
        $response->assertJsonStructure(['url']);
    }

    /** @test */
    public function testVerifyEmail()
    {
        // Create a user for testing
        $user = Factory::factoryForModel(User::class)->create([
            'verification_token' => 'verification_token_to_be_verified',
        ]);

        // Define the verification data
        $data = [
            'verification_token' => 'verification_token_to_be_verified',
            'redirect_url' => urlencode('http://example.com/verified-success'),
        ];

        // Send a GET request to the verify-email route
        $response = $this->get("api/auth/verify-email/{$data['verification_token']}/{$data['redirect_url']}");

        // Assert that the response status is a redirect (e.g., 302) or the appropriate status code
        $response->assertStatus(302);

        // Assert that the redirect URL matches the expected URL
        $response->assertRedirect('http://example.com/verified-success');
    }

    /** @test */
    public function testCreateUser()
    {
        // Define test data for the request
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ];

        // Make a POST request to the createUser route with the test data
        $response = $this->post('/api/auth/createUser', $userData, $this->adminHeaders);

        // Assert that the response has a status code of 201 (Created)
        $response->assertStatus(201);

        // Assert that the response JSON contains the expected message
        $response->assertJson(['message' => 'User successfully registered']);
    }
}
