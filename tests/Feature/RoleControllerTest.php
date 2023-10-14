<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Sticker;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Database\Seeders\RolesTableSeeder;


class RoleControllertest extends TestCase
{
    use RefreshDatabase; // Automatically reset the database after each test
    use WithFaker;       // Use Faker for generating fake data
    protected $headers = [];

    protected function setUp(): void
    {
        parent::setUp();

        // set the authentication for certain specific routes.
        Artisan::call('db:seed', ['--class' => RolesTableSeeder::class]);
        $user = User::factory()->create();
        $adminRole = Role::where('name', 'admin')->first();
        $user->roles()->attach($adminRole);
        $token = JWTAuth::fromUser($user);
        $headers = ['Authorization' => 'Bearer ' . $token];
        $this->setHeaders($headers);
    }

    // Custom method to set headers
    protected function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /** @test */
    public function it_can_list_all_role()
    {
        // Create some sticker records in the database
        Role::factory(3)->create();

        $response = $this->get('/api/roles', $this->headers);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'current_page',
                    'data',
                    'first_page_url',
                ]
            ]);
        $response->assertJsonCount(5, 'data.data');
    }

    /** @test */
    public function it_can_show_a_specific_role()
    {
        // Create a sticker record in the database
        $sticker = Role::create([
            'name' => 'Test role',
        ]);

        $response = $this->get('/api/roles/' . $sticker->id, $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Test role',
            ]);
    }

    /** @test */
    public function it_can_create_a_role()
    {
        $data = [
            'name' => 'Test role',
        ];

        $response = $this->postJson('/api/roles', $data, $this->headers);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Test role',
            ]);

        // Verify that the record exists in the database
        $this->assertDatabaseHas('roles', $data);
    }

    /** @test */
    public function it_can_update_a_role()
    {
        // Create a sticker record in the database
        $sticker = Role::create([
            'name' => 'Original name'
        ]);

        $data = [
            'name' => 'Updated name',
        ];

        $response = $this->putJson('/api/roles/' . $sticker->id, $data, $this->headers);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated name',
            ]);

        // Verify that the record has been updated in the database
        $this->assertDatabaseHas('roles', $data);
    }

    /** @test */
    public function it_can_delete_a_role()
    {
        // Create a sticker record in the database
        $sticker = Role::create([
            'name' => 'To Be Deleted'
        ]);

        $response = $this->delete('/api/roles/' . $sticker->id, [], $this->headers);

        $response->assertStatus(204);

        // Verify that the record has been deleted from the database
        $this->assertDatabaseMissing('roles', [
            'name' => 'To Be Deleted'
        ]);
    }
}
