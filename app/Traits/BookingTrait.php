<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;

trait BookingTrait
{
    private function executeBooking(
        string $callback,
        string $response,
        string $functionName,
        array $request = []
    ): JsonResponse {
        try {
            $this->getRepository()->$callback($request);

            return $this->getResponse(['status' => 'success'], $response);
        } catch (BadRequestException $e) {
            return $this->getResponse(
                [
                    'status' => 'failed',
                    'message' => $e->getMessage()
                ],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\Exception $e) {
            Log::error(__CLASS__ . '::' . $functionName . '::' . $e->getMessage());

            return $this->getResponse(
                ['status' => 'failed'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function getResponse(array $data, string $code): JsonResponse
    {
        return response()->json($data, $code);
    }
}
