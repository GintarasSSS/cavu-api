<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Place;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;
    private array $data = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'start_at' => now()->addDays(2)->toDateString(),
            'end_at' => now()->addDays(10)->toDateString()
        ];
    }

    public function testUserCanGetBookings(): void
    {
        $this
            ->getJson('/api/bookings?' . http_build_query($this->data))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'status',
                'data'
            ]);
    }

    public function testUnAuthenticatedUserCanNotCreateBooking(): void
    {
        $this->postJson('/api/bookings', $this->data)->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthenticatedUserCanCreateBooking(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();

        $this
            ->actingAs($user, 'sanctum')
            ->postJson('/api/bookings', $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status']);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'place_id' => $place->id,
            'start_at' => $this->data['start_at'],
            'end_at' => $this->data['end_at'],
        ]);
    }

    public function testUnAuthenticatedUserCanNotUpdateBooking(): void
    {
        $this->putJson('/api/bookings', $this->data)->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthenticatedUserCanUpdateBooking(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->putJson('/api/bookings', $this->data)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status']);

        $this->assertSoftDeleted('bookings', ['id' => $booking->id]);

        $this->assertDatabaseHas(
            'bookings',
            [
                'user_id' => $user->id,
                'place_id' => $place->id,
                'start_at' => $this->data['start_at'],
                'end_at' => $this->data['end_at'],
            ]
        );
    }

    public function testUnAuthenticatedUserCanNotDeleteBooking(): void
    {
        $this->deleteJson('/api/bookings')->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthenticatedUserCanDeleteBooking(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);

        $this
            ->actingAs($user, 'sanctum')
            ->deleteJson('/api/bookings')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['status']);

        $this->assertSoftDeleted('bookings', ['id' => $booking->id]);
    }

    public function testUnAuthenticatedUserCanNotGetBookingDetails(): void
    {
        $this->getJson('/api/bookings/details')->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthenticatedUserCanGetBookingDetails(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();
        Booking::factory()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);

        $response = $this
            ->actingAs($user, 'sanctum')
            ->getJson('/api/bookings/details')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'status',
                'message',
                'data'
            ]);

        $this->assertNotEmpty($response->json('data'));
    }

    #[DataProvider('endpointData')]
    public function testAuthenticatedUserSendsIncorrectPayload(string $method, string $url): void
    {
        $user = User::factory()->create();

        $this
            ->actingAs($user, 'sanctum')
            ->$method($url, [])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure(['message', 'errors'])
            ->assertJsonValidationErrors(['start_at', 'end_at']);
    }

    public static function endpointData(): array
    {
        return [
            'get all bookings' => ['getJson', '/api/bookings'],
            'create booking' => ['postJson', '/api/bookings'],
            'update booking' => ['putJson', '/api/bookings'],
        ];
    }
}
