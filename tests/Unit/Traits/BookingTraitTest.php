<?php

namespace Tests\Unit\Traits;

use App\Repositories\BookingRepository;
use App\Traits\BookingTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class BookingTraitTest extends TestCase
{
    use BookingTrait;

    private MockInterface $repository;
    private array $data = ['key' => 'value'];

    protected function setUp(): void
    {
        parent::setUp();
        Log::shouldReceive('error')->andReturnNull();

        $this->repository = Mockery::mock();
        $this->app->instance(BookingRepository::class, $this->repository);
    }

    public function testExecuteBookingSuccess()
    {
        $this->repository->shouldReceive('callback')->once()->with($this->data)->andReturn(true);

        $response = $this->executeBooking('callback', Response::HTTP_OK, 'testFunction', $this->data);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['status' => 'success'], $response->getData(true));
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public function testExecuteBookingBadRequestException()
    {
        $this->repository->shouldReceive('callback')->andThrow(new BadRequestException('Bad request'));

        $response = $this->executeBooking('callback', Response::HTTP_OK, 'testFunction');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['status' => 'failed', 'message' => 'Bad request'], $response->getData(true));
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testExecuteBookingGenericException()
    {
        $this->repository->shouldReceive('callback')->andThrow(new \Exception('Something went wrong'));

        $response = $this->executeBooking('callback', Response::HTTP_OK, 'testFunction');

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(['status' => 'failed'], $response->getData(true));
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testGetResponse()
    {
        $code = Response::HTTP_OK;

        $response = $this->getResponse($this->data, $code);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals($this->data, $response->getData(true));
        $this->assertEquals($code, $response->getStatusCode());
    }

    protected function getRepository()
    {
        return app(BookingRepository::class);
    }
}

