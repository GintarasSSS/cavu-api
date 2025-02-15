<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetBookingsRequest;
use App\Http\Requests\PostBookingsRequest;
use App\Http\Requests\PutBookingsRequest;
use App\Interfaces\BookingRepositoryInterface;
use App\Traits\BookingTrait;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BookingController extends Controller
{
    use BookingTrait;

    public function __construct(private readonly BookingRepositoryInterface $bookingRepository)
    {
    }

    public function index(GetBookingsRequest $request): JsonResponse
    {
        return $this->getResponse(
            [
                'status' => 'success',
                'data' => $this->bookingRepository->getAvailability($request->validated())
            ],
            Response::HTTP_OK
        );
    }

    public function store(PostBookingsRequest $request): JsonResponse
    {
        return $this->executeBooking('createBooking', Response::HTTP_OK, __FUNCTION__, $request->validated());
    }

    public function destroy(): JsonResponse
    {
        return $this->executeBooking('deleteBooking', Response::HTTP_OK, __FUNCTION__);
    }

    public function update(PutBookingsRequest $request): JsonResponse
    {
        return $this->executeBooking('updateBooking', Response::HTTP_OK, __FUNCTION__, $request->validated());
    }

    public function show(): JsonResponse
    {
        $booking = $this->bookingRepository->getBooking();

        return $this->getResponse(
            [
                'status' => $booking ? 'success' : 'failed',
                'message' => $booking ? '' : 'There are no available bookings.',
                'data' => $booking
            ],
            $booking ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST
        );
    }

    protected function getRepository(): BookingRepositoryInterface
    {
        return $this->bookingRepository;
    }
}
