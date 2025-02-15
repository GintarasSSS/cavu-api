<?php

namespace App\Repositories;

use App\Interfaces\BookingRepositoryInterface;
use App\Models\Booking;
use App\Models\Place;
use App\Models\Price;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookingRepository implements BookingRepositoryInterface
{
    private const PRICES_CACHE_KEY = 'prices';

    public function __construct(
        private readonly Carbon $carbon,
        private readonly Price $price,
        private readonly Place $place,
        private readonly Booking $booking
    ) {
    }

    public function getAvailability(array $request): array
    {
        $startDate = $this->carbon::parse($request['start_at']);
        $endDate = $this->carbon::parse($request['end_at']);

        $model = null;

        $prices = Cache::remember(self::PRICES_CACHE_KEY, Carbon::now()->addDay(), fn() => $this->price::all());

        while ($startDate->lte($endDate)) {
            $date = $startDate->toDateString();
            $datePrice = $this->calculateDatePrice($startDate, $prices);

            $query = $this->place::query()
                ->selectRaw("? as date", [$date])
                ->selectRaw('COUNT(places.id) as available')
                ->selectRaw('? as price', [$datePrice])
                ->whereNotIn('id', $this->getBookedPlaceIds($date));

            $model = $model ? $model->union($query) : $query;

            $startDate->addDay();
        }

        return $model->get() ? $model->get()->toArray() : [];
    }

    public function createBooking(array $request): void
    {
        if ($this->getBooking()) {
            throw new BadRequestException('Customer has already valid booking.');
        }

        if (!($place = $this->getPlace($request))) {
            throw new BadRequestException('There are not available places for selected days.');
        }

        $this->booking::create([
            'user_id' => auth()->user()->id,
            'place_id' => $place->id,
            'start_at' => $request['start_at'],
            'end_at' => $request['end_at']
        ]);
    }

    public function deleteBooking(): void
    {
        if (!($booking = $this->getBooking())) {
            throw new BadRequestException('Customer has not valid booking.');
        }

        $booking->delete();
    }

    public function updateBooking(array $request): void
    {
        $this->deleteBooking();
        $this->createBooking($request);
    }

    public function getBooking(): ?Booking
    {
        return $this->booking::where('user_id', auth()->user()->id)
            ->with(['place'])
            ->get()
            ->first();
    }

    private function getPlace(array $request): ?Place
    {
        return $this->place::with(['bookings' => function ($q) use ($request) {
            $q->whereNotBetween('start_at', [$request['start_at'], $request['end_at']])
                ->whereNotBetween('end_at', [$request['start_at'], $request['end_at']]);
        }])->get()->first();
    }

    private function calculateDatePrice($date, $prices): float
    {
        $datePrice = 0;

        foreach ($prices as $price) {
            if ($price->is_default) {
                $datePrice += $price->price;
            }

            if ($price->is_weekend && $date->isWeekend()) {
                $datePrice += $price->price;
            }

            if ($price->start_at && $price->end_at) {
                $priceStart = $this->carbon::parse($price->start_at);
                $priceEnd = $this->carbon::parse($price->end_at);

                if ($date->between($priceStart, $priceEnd)) {
                    $datePrice += $price->price;
                }
            }
        }

        return $datePrice / 100;
    }

    private function getBookedPlaceIds($date): Builder
    {
        return $this->booking::query()
            ->select('place_id')
            ->whereDate('start_at', '<=', $date)
            ->whereDate('end_at', '>=', $date);
    }
}
