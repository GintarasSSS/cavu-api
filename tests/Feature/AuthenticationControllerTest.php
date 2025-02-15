<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private string $password = 'password123';
    private string $email = 'test@example.com';

    public function testRegister()
    {
        $data = [
            'name' => 'Test User',
            'email' => $this->email,
            'password' => $this->password
        ];

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(Response::HTTP_CREATED)->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => $this->email
        ]);
    }

    public function testLoginSuccessful()
    {
        User::factory()->create([
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ]);

        $data = [
            'email' => $this->email,
            'password' => $this->password
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['status', 'token']);

        $this->assertEquals('success', $response->json('status'));
        $this->assertNotEmpty($response->json('token'));
    }

    public function testLoginFailed()
    {
        $data = [
            'email' => 'non_existing@example.com',
            'password' => 'wrongpassword'
        ];

        $response = $this->postJson('/api/login', $data);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertJson(['status' => 'failed']);
    }

    public function testLogout()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/logout');

        $response->assertStatus(Response::HTTP_OK)->assertJson(['status' => 'success']);
    }
}
