<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Place;
use App\Models\Price;
use App\Models\User;
use App\Repositories\BookingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class BookingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private BookingRepository $bookingRepository;
    private Carbon $carbon;
    private Price $price;
    private Place $place;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->carbon = new Carbon();
        $this->price = new Price();
        $this->place = new Place();
        $this->booking = new Booking();
        $this->bookingRepository = new BookingRepository($this->carbon, $this->price, $this->place, $this->booking);
    }

    public function testGetAvailability(): void
    {
        $request = [
            'start_at' => now()->toDateString(),
            'end_at' => now()->addDays(3)->toDateString(),
        ];

        $prices = Price::factory()->count(10)->create();

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($prices);

        $availability = $this->bookingRepository->getAvailability($request);

        $this->assertIsArray($availability);
        $this->assertCount(4, $availability);
    }

    public function testCreateBooking(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();

        $request = [
            'place_id' => $place->id,
            'start_at' => now()->toDateString(),
            'end_at' => now()->addDays(3)->toDateString(),
        ];

        auth()->login($user);

        $this->bookingRepository->createBooking($request);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'place_id' => $place->id,
            'start_at' => $request['start_at'],
            'end_at' => $request['end_at'],
        ]);
    }

    public function testDeleteBooking(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);

        auth()->login($user);

        $this->bookingRepository->deleteBooking();

        $this->assertSoftDeleted('bookings', [
            'id' => $booking->id,
        ]);
    }

    public function testUpdateBooking(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();
        $booking = Booking::factory()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);

        $request = [
            'place_id' => $place->id,
            'start_at' => now()->toDateString(),
            'end_at' => now()->addDays(5)->toDateString(),
        ];

        auth()->login($user);

        $this->bookingRepository->updateBooking($request);

        $this->assertSoftDeleted('bookings', [
            'id' => $booking->id,
        ]);

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'place_id' => $place->id,
            'start_at' => $request['start_at'],
            'end_at' => $request['end_at'],
            'deleted_at' => null
        ]);
    }

    public function testGetBooking(): void
    {
        $user = User::factory()->create();
        $place = Place::factory()->create();
        Booking::factory()->create([
            'user_id' => $user->id,
            'place_id' => $place->id,
        ]);

        auth()->login($user);

        $result = $this->bookingRepository->getBooking();

        $this->assertInstanceOf(Booking::class, $result);
    }
}
