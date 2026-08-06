<?php

namespace App\Trait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
   protected function success(mixed $data = null, string $message = 'Success', int $status = 200): JsonResponse 
   {
        $payload = [
            'success' => true,
            'status_code' => $status,
            'message' => $message,
        ];

        if (
            $data instanceof ResourceCollection &&
            $data->resource instanceof LengthAwarePaginator
        ) {
            $resolved = $data->response()->getData(true);

            $payload['data'] = $resolved['data'];

            $payload['pagination'] = [
                'per_page' => $resolved['meta']['per_page'],
                'total'    => $resolved['meta']['total'],
            ];
        } else {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    protected function successNoContent(string $message = 'Success'): JsonResponse 
    {
        return response()->json([
            'success' => true,
            'status_code' => 200,
            'message' => $message,
        ]);
    }
}
