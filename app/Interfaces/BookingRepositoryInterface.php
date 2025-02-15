<?php

namespace App\Interfaces;

use App\Models\Booking;

interface BookingRepositoryInterface
{
    public function getAvailability(array $request): array;
    public function createBooking(array $request): void;
    public function deleteBooking(): void;
    public function updateBooking(array $request): void;
    public function getBooking(): ?Booking;
}
