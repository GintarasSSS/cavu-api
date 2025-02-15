<?php

namespace Tests\Unit;

use App\Interfaces\AuthenticationRepositoryInterface;
use App\Models\User;
use App\Repositories\AuthenticationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AuthenticationRepositoryInterface $authRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authRepository = new AuthenticationRepository();
    }

    public function testRegisterCreatesAUser()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $this->authRepository->register($data);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com'
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function testLoginReturnsTokenOnSuccess()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123')
        ]);

        $data = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        $token = $this->authRepository->login($data);

        $this->assertNotEmpty($token);
    }

    public function testLoginReturnsEmptyStringOnFailure()
    {
        $data = [
            'email' => 'non_existing@example.com',
            'password' => 'wrongpassword'
        ];

        $token = $this->authRepository->login($data);

        $this->assertEmpty($token);
    }

    public function testLogoutDeletesTokens()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');

        $user->createToken('test');

        $this->authRepository->logout();

        $this->assertEmpty($user->tokens);
    }
}
